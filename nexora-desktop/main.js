const { app, BrowserWindow } = require("electron")
const { spawn } = require("child_process")
const path = require("path")
const http = require("http")
const fs = require("fs")
const crypto = require("crypto")

let phpServer
let splash
let mainWindow
let lastError = ""

function ensureEnv(laravelPath){

    const env = path.join(laravelPath,".env")
    const envExample = path.join(laravelPath,".env.example")

    if(!fs.existsSync(env) && fs.existsSync(envExample)){
        fs.copyFileSync(envExample,env)
    }

    if(!fs.existsSync(env)) return

    let content = fs.readFileSync(env,"utf8")

    const match = content.match(/^APP_KEY=(.*)$/m)

    if(!match || match[1].trim() === ""){

        const key = "base64:"+crypto.randomBytes(32).toString("base64")

        if(match){
            content = content.replace(/^APP_KEY=.*$/m,`APP_KEY=${key}`)
        }else{
            content += `\nAPP_KEY=${key}\n`
        }

        fs.writeFileSync(env,content)

    }

}

function getPaths(){

    const phpPath = app.isPackaged
        ? path.join(process.resourcesPath,"php","php.exe")
        : path.join(__dirname,"electron","php","php.exe")

    const laravelPath = app.isPackaged
        ? path.join(process.resourcesPath,"laravel")
        : path.join(__dirname,"laravel")

    ensureEnv(laravelPath)

    return {phpPath,laravelPath}

}

function showError(reason){

    const logPath = path.join(
        app.getPath("userData"),
        "runtime",
        "laravel",
        "storage",
        "logs",
        "laravel.log"
    )

    const html = `
    <html>
    <body style="font-family:Segoe UI;padding:30px">
        <h2>Falha ao iniciar o sistema</h2>
        <p>O backend Laravel nao respondeu corretamente.</p>
        <p><b>Motivo:</b> ${reason}</p>
        <p>Verifique o log em:</p>
        <pre>${logPath}</pre>
    </body>
    </html>
    `

    mainWindow.loadURL("data:text/html,"+encodeURIComponent(html))

    mainWindow.once("ready-to-show",()=>{
        if(splash) splash.destroy()
        mainWindow.show()
    })

}

function startPHP(){

    const {phpPath,laravelPath} = getPaths()

    console.log("PHP:",phpPath)
    console.log("Laravel:",laravelPath)

    phpServer = spawn(phpPath,[
        "artisan",
        "serve",
        "--host=127.0.0.1",
        "--port=8000"
    ],{
        cwd:laravelPath,
        shell:true
    })

    phpServer.stdout.on("data",(d)=>{
        console.log("PHP:",d.toString())
    })

    phpServer.stderr.on("data",(d)=>{
        const text = d.toString()
        lastError = text
        console.log("PHP ERROR:",text)
    })

}

function waitServer(){

    let attempts = 0
    const max = 60

    const check = ()=>{

        attempts++

        http.get("http://127.0.0.1:8000",(res)=>{

            if(res.statusCode >= 500){

                if(attempts >= max){
                    showError("HTTP 500 durante inicializacao")
                    return
                }

                setTimeout(check,1000)
                return
            }

            mainWindow.loadURL("http://127.0.0.1:8000")

            mainWindow.once("ready-to-show",()=>{
                if(splash) splash.destroy()
                mainWindow.show()
            })

        }).on("error",()=>{

            if(attempts >= max){
                showError(lastError || "Timeout ao iniciar servidor")
                return
            }

            setTimeout(check,1000)

        })

    }

    check()

}

function createWindow(){

    splash = new BrowserWindow({
        width:500,
        height:300,
        frame:false,
        resizable:false,
        alwaysOnTop:true
    })

    splash.loadFile(path.join(__dirname,"splash","splash.html"))

    mainWindow = new BrowserWindow({
        width:1200,
        height:800,
        show:false
    })

    waitServer()

}

app.whenReady().then(()=>{

    startPHP()
    createWindow()

})

app.on("window-all-closed",()=>{

    if(phpServer){
        phpServer.kill()
    }

    app.quit()

})
