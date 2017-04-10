# google_analytics_server_side_php
Send hints to Google analytics with no java script, server side.
# Server Side Google Analytics (SSGA) is a simple PHP 5 class, which allows to track server-side events and data within Google Analytics.
https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide

# Usage
create new ssga object
include 'lib/ss-ga.class.php';
$ssga = new ssga( 'UA-YOUR_NUMBER', 'yoursite.com' );

Set a pageview
$ssga->set_page( '/page.php' );
$ssga->set_page_title( 'Page Title' );

Send
$ssga->send();
Set an event (based on https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#event)

//$ssga as created above
$ssga->send_event( 'Feed', 'Categories', $label, $value );

Ecommerce tracking (https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#ecom)

$ssga_step1 = new ssga( 'UA-12345678-1','domain.tw' );
//$transaction_id, $affiliation, $total, $tax, $shipping, $curency
$ssga_step1->send_transaction("20159527001", "MXP", 280, 0, 80,"EUR");

$ssga_step2 = new ssga( 'UA-12345678-1','domain.tw' );
//$transaction_id, $sku, $product_name, $variation, $unit_price, $quantity
$ssga_step2->send_item("20159527001", "1229001", "TEST-PRODUCT", "", 50, 4);
