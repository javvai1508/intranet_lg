<?php

use Nette\Forms\Controls;
use Nette\Forms\Form;
use Nette\Forms\Validator;

// Fixed Czech defaults, including validation rules added automatically by Nette.
Validator::$messages = [
    Controls\CsrfProtection::Protection => 'Platnost relace vypršela. Vraťte se na úvodní stránku a zkuste to znovu.',
    Form::Equal => 'Zadejte %s.',
    Form::NotEqual => 'Tato hodnota nesmí být %s.',
    Form::Filled => 'Vyplňte toto pole.',
    Form::Blank => 'Toto pole musí zůstat prázdné.',
    Form::MinLength => 'Zadejte alespoň %d znaků.',
    Form::MaxLength => 'Zadejte nejvýše %d znaků.',
    Form::Length => 'Zadejte hodnotu dlouhou %d až %d znaků.',
    Form::Email => 'Zadejte platnou e-mailovou adresu.',
    Form::URL => 'Zadejte platnou URL adresu.',
    Form::Integer => 'Zadejte celé číslo.',
    Form::Float => 'Zadejte číslo.',
    Form::Min => 'Zadejte hodnotu větší nebo rovnou %d.',
    Form::Max => 'Zadejte hodnotu menší nebo rovnou %d.',
    Form::Range => 'Zadejte hodnotu od %d do %d.',
    Form::MaxFileSize => 'Velikost souboru smí být nejvýše %d bajtů.',
    Form::MaxPostSize => 'Odeslaná data překračují limit %d bajtů.',
    Form::MimeType => 'Soubor nemá požadovaný formát.',
    Form::Image => 'Soubor musí být obrázek ve formátu JPEG, GIF, PNG nebo WebP.',
    Controls\SelectBox::Valid => 'Vyberte platnou možnost.',
    Controls\UploadControl::Valid => 'Při nahrávání souboru došlo k chybě.',
];
