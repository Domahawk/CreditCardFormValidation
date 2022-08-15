<?php

namespace App\Services;

use DateTime;
use Exception;
use Throwable;

class FormValidationService
{
    private const AMERICAN_EXPRESS_IDENTIFIER = [
        34,
        37,
    ];
    private const AMERICAN_EXPRESS_CVV_LENGTH = 4;
    private const CVV_LENGTH = 3;

    public function validateForm(array $data): array
    {
        $creditCardNumber = $this->removeSpaces($data['cardNumber']);
        $isAmericanExpress = $this->isAmericanExpressCard($creditCardNumber);

        $errors = [];

        try {
            $this->validateExpiryDate((int) $data['expireMonth'], (int) $data['expireYear']);
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $this->validateCreditCardNumber($creditCardNumber);
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }

        try {
            $this->validateCvvNumber($data['cvv'], $isAmericanExpress);
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }

        if (!empty($errors)) {
            return $errors;
        }

        return ['success'];
    }

    private function validateExpiryDate(int $expireMonth, int $expireYear): void
    {
        $currenntDate = new DateTime();
        $currentYear = (int) $currenntDate->format('Y');
        $currentMonth = (int) $currenntDate->format('m');
        
        if ($currentYear > $expireYear) {
            throw new Exception('expireYear');
        }

        if ($currentYear === $expireYear && $currentMonth > $expireMonth) {
            throw new Exception('expireMonth');
        }
    }

    private function validateCreditCardNumber(string $creditCardNumber): void
    {
        $digitCount = strlen($creditCardNumber);

        if ($digitCount < 16 || $digitCount > 19 || !is_numeric($creditCardNumber)) {
            throw new Exception('cardNumber');
        }

        $this->checkLastDigit($creditCardNumber);
    }

    private function validateCvvNumber(string $cvv, bool $isAmericanExpress): void
    {
        $numberLenght = strlen((string) $cvv);

        if ($isAmericanExpress) {
            $this->checkAmericanExpressCvv($numberLenght);

            return;
        }

        if ($numberLenght !== self::CVV_LENGTH) {
            throw new Exception('cvv');
        }
    }

    private function isAmericanExpressCard(string $cardNumber): bool
    {
        $cardType = substr($cardNumber, 0, 2);

        if (in_array($cardType, self::AMERICAN_EXPRESS_IDENTIFIER)) {
            return true;
        }

        return false;
    }

    private function removeSpaces(string $subject): string
    {
        $noEmptySpaces = str_replace(' ', '', $subject);

        return $noEmptySpaces;
    }

    private function checkAmericanExpressCvv(int $lenght): void
    {
        if ($lenght !== self::AMERICAN_EXPRESS_CVV_LENGTH) {
            throw new Exception('cvv');
        }
    }

    private function checkLastDigit(string $cardNumber): void
    {
        $elements = str_split($cardNumber);
        $lastNumber = array_pop($elements);
        $cardNumberSum = $this->getCalculatedSum($elements);
        $numberFound = null;

        for ($i=0;$i <= 9;$i++) {
            if (($cardNumberSum + $i) % 10 === 0) {
                $numberFound = $i;

                break;
            }
        }

        if ((int) $lastNumber !== $numberFound) {
            throw new Exception('cardNumber');
        }
    }

    private function getCalculatedSum(array $elements): int
    {
        $result = 0;

        foreach ($elements as $key => $element) {
            if ($key % 2 !== 0) {
                $result += $element;

                continue;
            }

            $number = $element * 2;

            if ($number < 10) {
                $result +=  $number;

                continue;
            }

            $finalNumbers = str_split($number);
            $result += ($finalNumbers[0] + $finalNumbers[1]);
        }

        
        return $result;
    }
}