<?php

    /**
     * Validates data received from the contact form.
     * 
     * @param mixed $data - The data to validate.
     * @param string $field - The name of the field.
     * @param int $minLength - The minimum length of the data.
     * @param int $maxLength - The maximum length of the data.
     * @param int|null $minValue - The minimum value (optional, used only for 'number' fields).
     * @param int|null $maxValue - The maximum value (optional, used only for 'number' fields).
     * @param string $type - The type of the field.
     * 
     * @return array - An array containing validation errors.
     */
    function validate(mixed $data, string $field, int $minLength = 5, int $maxLength = 50, string $type = 'text', ?int $minValue = null, ?int $maxValue = null): array
    {
        $errors = [];

        if (empty($data)) {
            $errors[$field] = "The " . ucfirst($field) . " field is required.";
        } elseif (strlen($data) < $minLength) {
            $errors[$field] = "The " . ucfirst($field) . " field must be at least $minLength characters long.";
        } elseif (strlen($data) > $maxLength) {
            $errors[$field] = "The " . ucfirst($field) . " field must not exceed $maxLength characters.";
        } elseif ($type == 'email' && !filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $errors[$field] = "The " . ucfirst($field) . " field must be a valid email address.";
        } elseif ($type == 'number') {
            if (!is_numeric($data)) {
                $errors[$field] = "The " . ucfirst($field) . " field must be a number.";
            } elseif ($minValue !== null && $data < $minValue) {
                $errors[$field] = "The " . ucfirst($field) . " field must be greater than or equal to $minValue.";
            } elseif ($maxValue !== null && $data > $maxValue) {
                $errors[$field] = "The " . ucfirst($field) . " field must be less than or equal to $maxValue.";
            }
        }

        return $errors;
    }


    /**
     * Displays the error message for a specific field during validation.
     * 
     * @param string $field - The name of the field.
     * @param string $style - The CSS styles to apply to the error message.
     * 
     * @return string - The error message or an empty string.
     */
    function displayError($field, $style = 'color:red; font-size:14px;'): string
    {
        if (isset($_SESSION['errors'][$field])) {
            $error = "<span style='$style'>" . $_SESSION['errors'][$field] . "</span><br>";

            // Delete the error after displaying it
            unset($_SESSION['errors'][$field]);

            return $error;
        }
        return '';
    }

?>