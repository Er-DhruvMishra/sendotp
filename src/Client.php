<?php
declare(strict_types=1);

namespace SendOtp;

class Client
{
    private string $authKey;
    private ?string $sender = null;
    private int $otpLength;
    private int $otpExpiry;
    private ?string $message = null;

    /**
     * Constructor to initialize the SendOtp instance.
     *
     * @param string $authKey Your MSG91 authentication key.
     * @param int $otpLength Length of the OTP to be generated. Default is 4.
     * @param int $otpExpiry Expiry time of the OTP in minutes. Default is 15.
     */
    public function __construct(string $authKey, int $otpLength = 4, int $otpExpiry = 15)
    {
        $this->authKey = $authKey;
        $this->otpLength = $otpLength;
        $this->otpExpiry = $otpExpiry;
    }

    /**
     * Set the sender ID.
     *
     * @param string $sender
     * @return self
     */
    public function setSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Set a custom message.
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Send OTP to the specified mobile number.
     *
     * @param string $mobile Mobile number to which the OTP will be sent.
     * @param string $templateId The template ID for the OTP message.
     * @param string|null $otp Optionally provide an OTP. If not provided, one will be generated.
     * @return array Response from the API.
     */
    public function send(string $mobile, string $templateId, ?string $otp = null): array
    {
        if ($otp === null) {
            $otp = $this->generateOtp();
        }

        $url = "https://control.msg91.com/api/sendotp.php";

        $params = [
            'authkey'     => $this->authKey,
            'mobile'      => $mobile,
            'otp'         => $otp,
            'template_id' => $templateId,
            'otp_expiry'  => $this->otpExpiry,
        ];

        if ($this->sender !== null) {
            $params['sender'] = $this->sender;
        }

        if ($this->message !== null) {
            $params['message'] = $this->message;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== '') {
            return ['error' => $error];
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON response', 'raw_response' => $response];
        }

        return $decodedResponse;
    }

    /**
     * Generate a random OTP of the specified length.
     *
     * @return string
     */
    private function generateOtp(): string
    {
        $min = (int) str_pad('1', $this->otpLength, '0');
        $max = (int) str_pad('', $this->otpLength, '9');
        return (string) random_int($min, $max);
    }
}
