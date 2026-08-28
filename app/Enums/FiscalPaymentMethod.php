<?php

namespace App\Enums;

enum FiscalPaymentMethod: string
{
    case Cash = '01';
    case Cheque = '02';
    case CreditCard = '03';
    case DebitCard = '04';
    case StoreCredit = '05';
    case FoodVoucher = '10';
    case MealVoucher = '11';
    case GiftVoucher = '12';
    case FuelVoucher = '13';
    case Boleto = '15';
    case BankDeposit = '16';
    case Pix = '17';
    case BankTransfer = '18';
    case LoyaltyProgram = '19';
    case NoPayment = '90';
    case Other = '99';
}
