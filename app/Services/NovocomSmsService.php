<?php

namespace App\Services;

use App\Models\SmsTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class NovocomSmsService
{
    protected $baseUrl;
    protected $apiKey;
    protected $clientId;
    protected $senderId;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.novocom.base_url', 'https://sms.novocom-bd.com/api/v2');
        $this->apiKey = config('services.novocom.api_key');
        $this->clientId = config('services.novocom.client_id');
        $this->senderId = config('services.novocom.sender_id');
        $this->timeout = config('services.novocom.timeout', 30);
    }

    public function sendOtp($mobileNumber, $otp): array
    {
        $message = "Your Cake Town OTP is {$otp}";
        return $this->sendSms($mobileNumber, $message);
    }

    public function sendPromoCode($mobileNumber, $code, $discountRate, $startDate, $endDate): array
    {
//        $message = "FLASH DEAL! Use code ABCD to get !)TK OFF from today until {$endDate}";
        $message = "FLASH DEAL! Use code {$code} to get {$discountRate} OFF from {$startDate} until {$endDate}";
        return $this->sendSms($mobileNumber, $message);
    }

    /**
     * Send SMS to single or multiple numbers
     *
     * @param string|array $mobileNumbers
     * @param string $message
     * @param array $options
     * @return array
     */
    public function sendSms($mobileNumbers, string $message, array $options = []): array
    {
        try {
            // Format mobile numbers - ensure they start with 880
            $formattedNumbers = $this->formatMobileNumbers($mobileNumbers);

            $params = [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId,
                'SenderId' => "CAKE TOWN",
                'Message' => $message,
                'MobileNumbers' => is_array($formattedNumbers) ? implode(',', $formattedNumbers) : $formattedNumbers,
                'is_unicode' => true
            ];

            // Add optional parameters
            if (isset($options['is_unicode'])) {
                $params['Is_Unicode'] = $options['is_unicode'] ? 'true' : 'false';
            }

            if (isset($options['is_flash'])) {
                $params['Is_Flash'] = $options['is_flash'] ? 'true' : 'false';
            }

            if (isset($options['data_coding'])) {
                $params['DataCoding'] = $options['data_coding'];
            }

            if (isset($options['schedule_time'])) {
                $params['ScheduleTime'] = $options['schedule_time'];
            }

            if (isset($options['group_id'])) {
                $params['GroupId'] = $options['group_id'];
            }

            if (isset($options['validity_period'])) {
                $params['ValidityPeriod'] = $options['validity_period'];
            }

//            Log::info('Novocom SMS Request', [
//                'url' => "{$this->baseUrl}/SendSMS",
//                'params' => array_merge($params, ['ApiKey' => '***', 'ClientId' => '***'])
//            ]);

            // Use GET request as per API documentation
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$this->baseUrl}/SendSMS", $params);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom SMS Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    public function sendSms2($mobileNumbers, string $message, array $options = []): array
    {
        try {
            // Format mobile numbers - ensure they start with 880
            $formattedNumbers = $this->formatMobileNumbers($mobileNumbers);

            $params = [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId,
                'SenderId' => "CAKE TOWN",
                'Message' => $message,
                'MobileNumbers' => is_array($formattedNumbers) ? implode(',', $formattedNumbers) : $formattedNumbers,
                'is_unicode' => 'true'
            ];

            if (isset($options['is_flash'])) {
                $params['Is_Flash'] = $options['is_flash'] ? 'true' : 'false';
            }

            if (isset($options['data_coding'])) {
                $params['DataCoding'] = $options['data_coding'];
            }

            if (isset($options['schedule_time'])) {
                $params['ScheduleTime'] = $options['schedule_time'];
            }

            if (isset($options['group_id'])) {
                $params['GroupId'] = $options['group_id'];
            }

            if (isset($options['validity_period'])) {
                $params['ValidityPeriod'] = $options['validity_period'];
            }

//            Log::info('Novocom SMS Request', [
//                'url' => "{$this->baseUrl}/SendSMS",
//                'params' => array_merge($params, ['ApiKey' => '***', 'ClientId' => '***'])
//            ]);

            // Use GET request as per API documentation
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$this->baseUrl}/SendSMS", $params);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom SMS Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }


    /**
     * Send bulk SMS with different messages for each number
     *
     * @param array $messageParameters [['number' => '123', 'text' => 'message'], ...]
     * @param array $options
     * @return array
     */
    public function sendBulkSms(array $messageParameters, array $options = []): array
    {
        try {
            $formattedMessages = [];
            foreach ($messageParameters as $param) {
                $formattedMessages[] = [
                    'Number' => $param['number'],
                    'Text' => $param['text']
                ];
            }

            $data = [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId,
                'SenderId' => $options['sender_id'] ?? $this->senderId,
                'MessageParameters' => $formattedMessages,
                'Is_Unicode' => $options['is_unicode'] ?? false,
                'Is_Flash' => $options['is_flash'] ?? false,
                'IsRegisteredForDelivery' => $options['is_registered_for_delivery'] ?? true,
                'ValidityPeriod' => $options['validity_period'] ?? null,
                'DataCoding' => $options['data_coding'] ?? '0',
            ];

            if (isset($options['schedule_time'])) {
                $data['SchedTime'] = $options['schedule_time'];
            }

            // Remove null values
            $data = array_filter($data, function ($value) {
                return $value !== null;
            });

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/SendBulkSMS", $data);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Bulk SMS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Get message status by MessageId
     *
     * @param string $messageId
     * @return array
     */
    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get("{$this->baseUrl}/MessageStatus", [
                    'ApiKey' => $this->apiKey,
                    'ClientId' => $this->clientId,
                    'MessageId' => $messageId
                ]);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Message Status Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Get sent messages list with pagination and date filter
     *
     * @param int $start
     * @param int $length
     * @param string|null $fromDate (yyyy-mm-dd)
     * @param string|null $endDate (yyyy-mm-dd)
     * @return array
     */
    public function getSentMessages(int $start = 0, int $length = 100, ?string $fromDate = null, ?string $endDate = null): array
    {
        try {
            $params = [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId,
                'start' => $start,
                'length' => $length
            ];

            if ($fromDate) {
                $params['fromdate'] = $fromDate;
            }

            if ($endDate) {
                $params['enddate'] = $endDate;
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get("{$this->baseUrl}/SMS", $params);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Get Messages Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Get list of sender IDs
     *
     * @return array
     */
    public function getSenderIds(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get("{$this->baseUrl}/SenderId", [
                    'ApiKey' => $this->apiKey,
                    'ClientId' => $this->clientId
                ]);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Get Sender IDs Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Create new sender ID
     *
     * @param string $senderId
     * @param string $purpose
     * @return array
     */
    public function createSenderId(string $senderId, string $purpose): array
    {
        try {
            $data = [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId,
                'SenderId' => $senderId,
                'Purpose' => $purpose
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/SenderId", $data);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Create Sender ID Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Update sender ID
     *
     * @param int $id
     * @param string $senderId
     * @param string $purpose
     * @return array
     */
    public function updateSenderId(int $id, string $senderId, string $purpose): array
    {
        try {
            $data = [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId,
                'SenderId' => $senderId,
                'Purpose' => $purpose
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put("{$this->baseUrl}/SenderId?id={$id}", $data);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Update Sender ID Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Delete sender ID
     *
     * @param int $id
     * @return array
     */
    public function deleteSenderId(int $id): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->delete("{$this->baseUrl}/SenderId", [
                    'ApiKey' => $this->apiKey,
                    'ClientId' => $this->clientId,
                    'id' => $id
                ]);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('Novocom Delete Sender ID Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Handle API response
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return array
     */
    protected function handleResponse($response): array
    {
        $data = $response->json();

        if ($response->successful() && isset($data['ErrorCode']) && $data['ErrorCode'] == 0) {
            return [
                'success' => true,
                'data' => $data['Data'] ?? [],
                'message' => $data['ErrorDescription'] ?? 'Success'
            ];
        }

        $errorCode = $data['ErrorCode'] ?? 'UNKNOWN';
        $errorMessage = $data['ErrorDescription'] ?? 'Unknown error occurred';

        Log::warning("Novocom API Error: [{$errorCode}] {$errorMessage}");

        return [
            'success' => false,
            'error' => $errorMessage,
            'error_code' => $errorCode,
            'data' => $data['Data'] ?? []
        ];
    }

    /**
     * Get error message by error code
     *
     * @param string $errorCode
     * @return string
     */
    public function getErrorMessage(string $errorCode): string
    {
        $errors = [
            '0' => 'Success',
            '001' => 'Account details cannot be blank',
            '003' => 'SenderId cannot be blank',
            '004' => 'Message cannot be blank',
            '005' => 'Message properties cannot be blank',
            '006' => 'Internal Server Error Occurred',
            '007' => 'Invalid API Credentials',
            '009' => 'User account locked, contact your Administrator',
            '010' => 'Unauthorized API access',
            '011' => 'Unauthorized IP address',
            '012' => 'Message length violation',
            '013' => 'Invalid mobile numbers',
            '015' => 'Invalid SenderId',
            '017' => 'Invalid groupid',
            '018' => 'Group Not Allowed in BulkSMS',
            '019' => 'Invalid schedule date',
            '020' => 'Message or mobile number cannot be blank',
            '023' => 'Parameter missing',
            '024' => 'Invalid template or template mismatch',
            '026' => 'Invalid date range',
            '028' => 'Group not found',
            '029' => 'Record already exist',
            '033' => 'Queue Connection Closed',
            '034' => 'Unable to create campaign at this time please try again later',
            '036' => 'Error While Publishing DLR',
            '041' => 'Report Not Found',
            '042' => 'Max Mobile Number limit exceeded',
            '043' => 'Invalid Validity Period',
            '1025' => 'Insufficient Credits',
            '1026' => 'Invalid Template while message processing',
            '1029' => 'Invalid SenderId while message processing',
            '1044' => 'Spam Message Detected',
            '1047' => 'User account inactive',
            '1081' => 'Country not found in master data',
            '1082' => 'Network not found',
            '1083' => 'Price not found',
            '1084' => 'Expired',
            '1085' => 'Route not found',
            '1086' => 'Failover route not found',
            '1087' => 'Failover expired',
            '1088' => 'Failover price not found',
            '1089' => 'Failover failed',
            '1090' => 'Account Validity Expired',
            '1091' => 'Encoding Error',
            '1092' => 'OA/DA Error',
            '1093' => 'Queue Message Expired',
            '1094' => 'Invalid HTTP gateway config',
            '1095' => 'HTTP Request Exception',
        ];

        return $errors[$errorCode] ?? 'Unknown error';
    }

    public function formatMobileNumbers($mobileNumbers)
    {
        if (is_array($mobileNumbers)) {
            return array_map([$this, 'formatSingleNumber'], $mobileNumbers);
        }
        return $this->formatSingleNumber($mobileNumbers);
    }

    /**
     * Format a single mobile number
     *
     * @param string $number
     * @return string
     */
    protected function formatSingleNumber(string $number): string
    {
        // Remove any spaces, dashes, or special characters
        $number = preg_replace('/[^0-9]/', '', $number);

        // If number starts with 0, replace with 880
        if (substr($number, 0, 1) === '0') {
            $number = '880' . substr($number, 1);
        }
        // If number doesn't start with 880, add it
        elseif (substr($number, 0, 3) !== '880') {
            $number = '880' . $number;
        }

        return $number;
    }

    public function fetchTemplates()
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/Template", [
                'ApiKey' => $this->apiKey,
                'ClientId' => $this->clientId
            ]);

            if ($response->successful() && $response->json('ErrorCode') === 0) {
                $templates =  $response->json('Data');
                foreach ($templates as $template) {

                    SmsTemplate::updateOrCreate(
                        ['template_id' => $template['TemplateId']],
                        [
                            'template_name' => $template['TemplateName'],
                            'message_template' => $template['MessageTemplate'],
                            'is_approved' => $template['IsApproved'],
                            'is_active' => $template['IsActive'],
                            'dlt_template_id' => $template['DltTemplateId'],
                            'last_synced_at' => now()
                        ]
                    );
                }
                return $templates;
            }

            Log::error('Novocom API Error', [
                'response' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Novocom API Exception', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    public static function countVariables($template)
    {
        return substr_count($template, '{#var#}');
    }

    public static function replaceVariables($template, $values)
    {
        $result = $template;
        $index = 0;

        // Replace each {#var#} occurrence sequentially
        while (strpos($result, '{#var#}') !== false && isset($values[$index])) {
            $result = preg_replace('/\{#var#\}/', $values[$index], $result, 1);
            $index++;
        }

        return $result;
    }
}
