<?php

include_once '../conf/config.inc.php'; // app config
include_once '../lib/_functions.inc.php'; // app functions
include_once '../lib/classes/Db.class.php'; // db connector, queries

if (!isset($TEMPLATE)) {
  $TITLE = 'NetQuakes Sign Up';
  $NAVIGATION = true;
  $HEAD = '
    <link rel="stylesheet" href="/lib/leaflet-0.7.7/leaflet.css" />
    <link rel="stylesheet" href="css/signup.css" />
  ';
  $FOOT = '
    <script>
      var MOUNT_PATH = "' . $MOUNT_PATH . '";
    </script>
    <script src="/lib/leaflet-0.7.7/leaflet.js"></script>
    <script src="js/signup.js"></script>
  ';

  include 'template.inc.php';
}

// Initialize variables; set defaults
date_default_timezone_set('America/Los_Angeles');
$datetime = date('Y-m-d H:i:s');
$posting = false;

if (isSet($_POST['submit'])) { // user submitted form
  $posting = true;

  $fields = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'affiliation' => $_POST['affiliation'],
    'phone' => $_POST['phone'],
    'address1' => $_POST['address1'],
    'address2' => $_POST['address2'],
    'city' => $_POST['city'],
    'state' => $_POST['state'],
    'postcode' => $_POST['postcode'],
    'hearabout' => $_POST['hearabout'],
    'wifi' => $_POST['wifi'],
    'comment' => $_POST['comment'],
    'glat' => $_POST['glat'],
    'glon' => $_POST['glon'],
    'gaccuracy' => $_POST['gaccuracy'],
    'region' => $_POST['region'],
    'maddress1' => $_POST['maddress1'],
    'maddress2' => $_POST['maddress2'],
    'mcity' => $_POST['mcity'],
    'mstate' => $_POST['mstate'],
    'mpostcode' => $_POST['mpostcode']
  ];

  // if no Mailing Address provided, use Installation Address
  if (!$_POST['maddress1']) {
    $fields['maddress1'] = $_POST['address1'];
    $fields['maddress2'] = $_POST['address2'];
    $fields['mcity']     = $_POST['city'];
    $fields['mstate']    = $_POST['state'];
    $fields['mpostcode'] = $_POST['postcode'];
  }

  // Insert record
  $stmt = $pdo->prepare('INSERT INTO nca_netq_volunteers (datetime, name, email,
    affiliation, phone, address1, address2, city, state, postcode, hearabout,
    wifi, comment, glat, glon, gaccuracy, region, maddress1, maddress2, mcity,
    mstate, mpostcode) VALUES (:datetime, :name, :email, :affiliation, :phone,
    :address1, :address2, :city, :state, :postcode, :hearabout, :wifi, :comment,
    :glat, :glon, :gaccuracy, :region, :maddress1, :maddress2, :mcity,
    :mstate, :mpostcode)'
  );
  try {
    //$stmt->execute($fields);
  } catch (Exception $e) {
    print "Error: $e->getMessage()";
  }
  // Create summary html
  $return_html .= '<ul class="no-style results">
      <li><h4>Name</h4> ' . htmlentities(stripslashes($fields['name'])) . '</li>
      <li><h4>Affiliation</h4> ' . htmlentities(stripslashes($fields['affiliation'])) . '</li>
      <li><h4>Email</h4> ' . htmlentities(stripslashes($fields['email'])) . '</li>
      <li><h4>Phone</h4> ' . htmlentities(stripslashes($fields['phone'])) . '</li>
      <li><h4>Address 1</h4> ' . htmlentities(stripslashes($fields['address1'])) . '</li>
      <li><h4>Address 2</h4> ' . htmlentities(stripslashes($fields['address2'])) . '</li>
      <li><h4>City</h4> ' . htmlentities(stripslashes($fields['city'])) . '</li>
      <li><h4>State</h4> ' . htmlentities(stripslashes($fields['state'])) . '</li>
      <li><h4>Zip</h4> ' . htmlentities(stripslashes($fields['zip'])) . '</li>
    </ul>';

  print $return_html;
  return;
} else {
  $statelist = getStateList();
}
?>

<p>Although we don&rsquo;t anticipate performing many new installations, we
  continue to look for volunteers to host NetQuakes instruments. In particular,
  we are interested in finding volunteers living in
  <strong>regions highlighted in red</strong> on the map below.</p>

<div class="map"></div>

<h2>Volunteer to Host a NetQuakes Seismograph</h2>
<p>Please note: we are not currently looking for volunteers living outside the U.S.</p>

<h4>To host a NetQuakes instrument, you must provide:</h4>
<ul>
  <li>An out-of-the-way location in a 1-2 story building (no significant basement) with less than
  ~4000 sq feet in plan; building must have a concrete slab foundation in some location (for example, a garage)
  to which the NetQuakes box can be bolted.
  Buildings within a half mile of significant business districts and those near urban or suburban faults
  (such as the Hayward fault in the East Bay) are highly desirable.</li>
  <li>A local network with a permanent broadband connection to the internet. If you don't have WiFi, we will install
  a WiFi router.</li>
  <li>AC power to the seismograph.</li>
  <li>Occasional minor servicing of the instrument, such as battery replacement.</li>
</ul>
<p>If your site is selected to host a NetQuakes seismograph, you will be asked to <a href="docs/Revocable_Permit.pdf">sign an agreement</a> that describes your responsibilities and those of the USGS.</p>

<div class="alert">
  <input type="checkbox" value="false" tabindex="" id="toggle">
  <label for="toggle">My mailing address is different than the installation address.</label>
</div>

<form method="post" action="./signup" name="volunteer" id="volunteer">
  <div class="row">

    <div class="column one-of-two">
      <h3>Contact Details</h3>
      <label for="name">Name <span>*</span></label>
      <input type="text" required="required" name="name" id="name" tabindex="1" />
      <label for="affiliation">Affiliation</label>
      <input type="text" name="affiliation" id="affiliation" tabindex="2" />
      <label for="email">Email address <span>*</span></label>
      <input type="text" required="required" pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A_Za-z]{2,4}$" name="email" id="email" tabindex="3" />
      <label for="phone">Phone number <span>*</span></label>
      <input type="number" required="required" name="phone" id="phone" tabindex="4" />
      <h3>Optional</h3>
      <label for="hearabout">Where did you hear about the NetQuakes program?</label>
      <select name="hearabout" id="hearabout" tabindex="5" >
        <option value=""></option>
        <option value="web">web</option>
        <option value="TV">TV</option>
        <option value="radio">radio</option>
        <option value="newspaper">newspaper</option>
        <option value="family or friends">family or friends</option>
        <option value="other">other</option>
      </select>
      <label for="wifi">Do you currently have a WiFi router?</label>
      <select name="wifi" id="wifi" tabindex="6" >
        <option value=""></option>
        <option value="yes">yes</option>
        <option value="no">no</option>
      </select>
      <label for="comment">Comments</label>
      <textarea name="comment" id="comment" tabindex="7" rows="4"></textarea>
    </div>

    <div class="disabled mailing column one-of-two">
      <h3>Mailing Address</h3>
      <p class="note">Where we can contact you</p>
      <label for="maddress1">Address 1 <span>*</span></label>
      <input type="text" disabled="disabled" required="required" name="maddress1" id="maddress1" tabindex="8" />
      <label for="maddress2">Address 2</label>
      <input type="text" disabled="disabled" name="maddress2" id="maddress2" tabindex="9" />
      <label for="mcity">City <span>*</span></label>
      <input type="text" disabled="disabled" required="required" name="mcity" id="mcity" tabindex="11" />
      <label for="mstate">State <span>*</span></label>
      <select name="mstate" disabled="disabled" required="required" id="mstate" tabindex="11" >
        <?php print $statelist; ?>
      </select>
      <label for="mpostcode">Zip code <span>*</span></label>
      <input type="number" disabled="disabled" required="required" name="mpostcode" id="mpostcode" tabindex="12" />
    </div>

    <div class="column one-of-two">
      <h3>Installation Address</h3>
      <p class="note">Where we install the seismograph</p>
      <label for="address1">Address 1 <span>*</span></label>
      <input type="text" required="required" name="address1" id="address1" tabindex="13" />
      <label for="address2">Address 2</label>
      <input type="text" name="address2" id="address2" tabindex="14" />
      <label for="city">City <span>*</span></label>
      <input type="text" required="required" name="city" id="city" tabindex="15" />
      <label for="state">State <span>*</span></label>
      <select name="state" required="required" id="state" tabindex="16" >
        <?php print $statelist; ?>
      </select>
      <label for="postcode">Zip code <span>*</span></label>
      <input type="number" required="required" name="postcode" id="postcode" tabindex="17" />
      <label for="country">Country</label>
      <input type="text" value="United States" disabled="disabled" name="country" id="country" tabindex="" />
    </div>

  </div>
  <p class="required">* = Required Info</p>
  <button form="volunteer" name="submit" id="submit" class="green" tabindex="18">Volunteer</button>
</form>
