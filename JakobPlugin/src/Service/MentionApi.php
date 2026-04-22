<?php declare(strict_types=1);

namespace JakobPlugin\Service;


use DOMDocument;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
class MentionApi
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private function generate_request_body_receipts($customerNumber, $orderNumber): array
    {
        $user = "austria";
        $endpoint = 'receipts';
        $api_key = "yE5WQ4lvs9TTNj9f815DW9Deat5bm2bBT5bpoBop";
        $separator = ":::";
        $action = "getInvoice";
        $hash_string = $api_key . $separator . $endpoint . $separator . $action;
        $secure = hash("sha256", $hash_string);
        return array(
            "endpoint" => $endpoint,
            "action" => $action,
            "user" => $user,
            "secure" => $secure,
            "customer_number" => $customerNumber,
            "order_number" => $orderNumber,
            "multi_result" => 1,
        );
    }

    private function generate_request_body_open_orders($customer_number): array
    {
        $action = "getOpenList";
        $user = "austria";
        $endpoint = 'receipts';
        $api_key = "yE5WQ4lvs9TTNj9f815DW9Deat5bm2bBT5bpoBop";
        $separator = ":::";
        $hash_string = $api_key . $separator . $endpoint . $separator . $action;
        echo $hash_string;
        $secure = hash("sha256", $hash_string);
        echo $secure;
        return array(
            "endpoint" => $endpoint,
            "action" => $action,
            "user" => $user,
            "secure" => $secure,
            "customer_number" => $customer_number,
        );
    }


    public function get_german_data($order_number): array
    {
        $body_eur = $this->generate_request_body_receipts("87646", $order_number);
        $body_usd = $this->generate_request_body_receipts("90786", $order_number);
        $response_eur = $this->client->request('POST', 'https://edi.allnet.de/mention_api/', ["body" => $body_eur]);
        $response_usd = $this->client->request('POST', 'https://edi.allnet.de/mention_api/', ["body" => $body_usd]);

        $orders = array_merge($response_eur->toArray(), $response_usd->toArray());
        unset($orders["error"]);
        return $orders;
    }

    public function getAllOpenOrders(): array
    {
        $body_eur = $this->generate_request_body_open_orders("87646");
        $response_eur = $this->client->request('POST', 'https://edi.allnet.de/mention_api/', ["body" => $body_eur]);
        $body_usd = $this->generate_request_body_open_orders("90786");
        $response_usd = $this->client->request('POST', 'https://edi.allnet.de/mention_api/', ["body" => $body_usd]);

        return array_merge($response_eur->toArray(), $response_usd->toArray());

    }

    public function getOpenPositions(string $orderNumber, $openOrders): array
    {
        $result = [];
        $positions = $this->extractPositionsFromOrderNumber($openOrders, $orderNumber);
        foreach ($positions as $position) {
            $articleInfo = [];
            $articleNumber = $position["order_number"];
            if ($position["backlog"] == 1) {
                $articleInfo["inStock"] = 0;
                $articleInfo["backlog"] = $position["quantity"];
                $articleInfo["expected"] = $position["expected"]["date"] ?? null;
            } else {
                $articleInfo["inStock"] = $position["quantity"];
                $articleInfo["backlog"] = 0;
            }


            if (isset($result[$articleNumber])) {
                $result[$articleNumber]["inStock"] = $result[$articleNumber]["inStock"] + $articleInfo["inStock"];
                $result[$articleNumber]["backlog"] = $result[$articleNumber]["backlog"] + $articleInfo["backlog"];
            } else {
                $result[$articleNumber] = $articleInfo;
            }
        }
        print_r($result);
        return $result;
    }

    public function checkIfOrderInProgress(string $orderNumber, $openOrders): bool {
        foreach ($openOrders as $order) {
            if ($order["order_number"] == $orderNumber) {
                if ($order["receipt_state"] === "N" or $order["receipt_state"] === "F") {
                    return True;
                }
            }
        }
        return false;
    }

    private function extractPositionsFromOrderNumber(array $orders, string $orderNumber): array
    {
        $result = [];
        foreach ($orders as $order) {
            if ($order["order_number"] == $orderNumber) {
                $result = array_merge($result, $order["positions"]);
            }
        }
        return $result;
    }

    public function getBackendPriceData(): void
    {
        $browser = new HttpBrowser(HttpClient::create());

        $browser->request('POST', 'https://shop.allnet.at/backend/Login/login', [
            'username' => 'jlassnig',
            'password' => 'allnet123',
        ]);
        $response = $browser->getResponse();
        echo $response->getStatusCode();
        echo $browser->getResponse()->getContent();
        $browser->request('POST', 'https://shop.allnet.at/backend/AtPriceCalc/articlefixprice', [
            "download" => "1"
        ]);
        echo $response->getStatusCode();
        echo $browser->getResponse()->getContent();


    }
    public function getBackendPriceHtml(): array
    {
        $browser = new HttpBrowser(HttpClient::create());

        $browser->request('POST', 'https://shop.allnet.at/backend/Login/login', [
            'username' => 'jlassnig',
            'password' => 'allnet123',
        ]);
        $response = $browser->getResponse();
        echo $response->getStatusCode();
        echo $browser->getResponse()->getContent();
        $browser->request('GET', 'https://shop.allnet.at/backend/AtPriceCalc');
        echo $response->getStatusCode();
        $html = $browser->getResponse()->getContent();
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $divs = $doc->getElementsByTagName("div");
        $discounts = [];
        foreach ($divs as $div) {
            $singleEntry = [];
            if ($div->getAttribute("class") == "row border-bottom") {

                $inputs = $div->getElementsByTagName("input");
                foreach ($inputs as $input) {
                    if ($input->getAttribute("name") == "customer") {
                        $customerNumber = $input->getAttribute("value");
                       $singleEntry[] = $customerNumber;
                    }
                    echo " ";
                    if ($input->getAttribute("name") == "modifier") {
                        $discount = $input->getAttribute("value");
                        $singleEntry[] = $discount;
                    }
                    echo " ";
                }
                $options = $div->getElementsByTagName("option");
                foreach ($options as $option) {
                    if ($option->hasAttribute("selected")) {
                        $manufacturer = $option->nodeValue;
                        $singleEntry[] = $manufacturer;
                        echo " ";
                    }
                }
                echo "\n";
                $discounts[] = $singleEntry;
            }
        }
        print_r($discounts);
        return $discounts;
    }


};



