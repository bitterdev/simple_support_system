<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Cache\Level\ExpensiveCache;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Support\Facade\Application;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$app = Application::getFacadeApplication();
/** @var ExpensiveCache $expensiveCache */
/** @noinspection PhpUnhandledExceptionInspection */
$expensiveCache = $app->make(ExpensiveCache::class);
/** @var CookieJar $cookie */
/** @noinspection PhpUnhandledExceptionInspection */
$cookie = $app->make('cookie');

$productList = $expensiveCache->getItem("BitterProductList");

$products = [];

if ($productList->isMiss()) {
    $client = new Client([
        'base_uri' => 'https://bitter.de',
        'timeout' => 10.0,
    ]);

    $json = null;

    try {
        $response = $client->request('GET', '/index.php/api/v1/payments/products/get_products', [
            'query' => ['locale' => 'en_US'],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $contentType = $response->getHeaderLine('Content-Type');

            if ($contentType === 'application/json') {
                $body = $response->getBody()->getContents();
                $json = json_decode($body, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $json = null;
                }
            }
        }

    } catch (GuzzleException $e) {

    }

    if (is_array($json) &&
        isset($json["products"]) &&
        is_array($json["products"]) &&
        count($json["products"]) > 0) {
        $products = $json["products"];
        $productList->lock();
        $expensiveCache->save($productList->set($products)->expiresAfter(60 * 60 * 24));
    }
} else {
    $products = $productList->get();
}

if (!isset($products) || !is_array($products)) {
    $products = [];
}

$randomKey = array_rand($products);
$randomProduct = $products[$randomKey];

$name = $randomProduct["name"] ?? null;
$shortDescription = $randomProduct["shortDescription"] ?? null;
$marketplaceUrl = $randomProduct["attributes"]["concrete_marketplace_url"] ?? null;
$displayNotice = !$cookie->has("hideDidYouKnowAlert");
?>

<?php if ($displayNotice && $name !== null && $shortDescription !== null && $marketplaceUrl !== null) { ?>
    <div id="did-you-know">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php /** @noinspection HtmlUnknownTarget */
            echo t("Discover even more high-quality add-ons for Concrete CMS that will take your projects to the next level! Have you checked out the %s add-on yet? Find out more %s!",
                sprintf(
                    "<a href=\"%s\" target='_blank'>%s</a>",
                    h($marketplaceUrl),
                    htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                ),
                sprintf(
                    "<a href=\"%s\" target='_blank'>%s</a>",
                    h($marketplaceUrl),
                    t("here")
                )
            ); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <style>
        #did-you-know .alert a {
            color: var(--bs-alert-color);
            font-weight: bold;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const didYouKnowEl = document.getElementById('did-you-know');

            const closeBtn = didYouKnowEl.querySelector('.btn-close');

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    if (location.protocol === 'https:') {
                        document.cookie = "hideDidYouKnowAlert=true; path=/; max-age=31536000; SameSite=Lax; secure";
                    } else {
                        document.cookie = "hideDidYouKnowAlert=true; path=/; max-age=31536000; SameSite=Lax";
                    }

                    didYouKnowEl.classList.add('d-none');
                });
            }
        });
    </script>
<?php } ?>