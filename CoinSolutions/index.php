<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
		<link rel="icon" href="images/favicon.ico" type="image/x-icon">
		<title>Wind-Track</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="Real-time wind measurements" />
		<meta name="keywords" content="Windtrack,Wind-Track,Wind measurements,Real-time wind" />
		<meta name="author" content="Ioannis Petridis" />
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.poptrox.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-xlarge.css" />
		</noscript>
		<script src="http://maps.google.com/maps/api/js"
		type="text/javascript">
		</script>
		
		<script type="text/javascript">
			
			var customIcons = {
				restaurant: {
					icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png',
					shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
				},
				bar: {
					icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png',
					shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
				}
			};
			
			var markersArray = [];
			var map;
			var infoWindow;
			var q;
			<?php
				header("Content-type: text/html");
				header("Access-Control-Allow-Origin: *");

				echo "var name,days,email,velocity,altitude;";
				if (isset($_GET['q'])) {
					$q = $_GET['q'];
					if ($q=='ALL') {
						echo "var q = 'ALL';";
					}
					else {
						echo "var q;";
					}
				}
				else if (isset($_GET['name']) && is_string($_GET['name'])) {
					$name = $_GET['name'];
					echo "name = '".$name."';";
				}
				else if (isset($_GET['days']) && is_numeric($_GET['days'])) {
					$days = $_GET['days'];
					echo "days = ".$days.";";
				}
				else if (isset($_GET['email']) && is_string($_GET['email'])) {
					$email = $_GET['email'];
					echo "email = '".$email."';";
				}
				else if (isset($_GET['velocity']) && is_numeric($_GET['velocity'])) {
					$velocity = $_GET['velocity'];
					echo "velocity = '".$velocity."';";
				}
				else if (isset($_GET['altitude']) && is_numeric($_GET['altitude'])) {
					$altitude = $_GET['altitude'];
					echo "altitude = '".$altitude."';";
				}
			?> 
			
			function downloadUrl(url, callback) {
				var request = window.ActiveXObject ?
				new ActiveXObject('Microsoft.XMLHTTP') :
				new XMLHttpRequest;
				
				request.onreadystatechange = function() {
					if (request.readyState == 4) {
						request.onreadystatechange = doNothing;
						callback(request, request.status);
					}
				};
				
				request.open('GET', url, true);
				request.send(null);
			}
			
			function load() {
				map = new google.maps.Map(document.getElementById("map"), {
					center: new google.maps.LatLng(38, 22),
					zoom: 2,
					mapTypeId: 'terrain'
				});
				infoWindow = new google.maps.InfoWindow;
				if (q=='ALL') {
					downloadUrl("Scripts/Data_to_xml.php?q=ALL", processXML);
				}
				else if(name) {
					downloadUrl("Scripts/Data_to_xml.php?name="+name, processXML);
				}
				else if(days) {
					downloadUrl("Scripts/Data_to_xml.php?days="+days, processXML);
				}
				else if(email) {
					downloadUrl("Scripts/Data_to_xml.php?email="+email, processXML);
				}
				else if(velocity) {
					downloadUrl("Scripts/Data_to_xml.php?velocity="+velocity, processXML);
				}
				else if(altitude) {
					downloadUrl("Scripts/Data_to_xml.php?altitude="+altitude, processXML);
				}
				else {
					downloadUrl("Scripts/Data_to_xml.php", processXML);
				}
			}
			
			function processXML(data) {
				var xml = data.responseXML;
				var markers = xml.documentElement.getElementsByTagName("measurement");
				//Clear markers before drawing new ones
				resetMarkers(markersArray);
				for (var i = 0; i < markers.length; i++) {
					var name = markers[i].getAttribute("name");
					var timestamp = markers[i].getAttribute("timestamp");			
					var velocity = markers[i].getAttribute("velocity");
					var direction = markers[i].getAttribute("direction");
					var comment = markers[i].getAttribute("comment");
					var type = "restaurant"; //icon
					var point = new google.maps.LatLng(
					parseFloat(markers[i].getAttribute("latitude")),
					parseFloat(markers[i].getAttribute("longitude")));
					var marker_content = "<b>User</b>: " + name.toString() +"<br/><b>Velocity</b>: " + velocity.toString() + "<br/><b>Direction</b>: "+ direction.toString()+ "<br/><b>Time</b>: "+ timestamp.toString()+ "<br/><br/><center><i>'"+comment+"'</i></center>";
					var icon = customIcons[type] || {};
					var marker = new google.maps.Marker({
						map: map,
						position: point,
						icon: icon.icon
					});
					markersArray.push(marker);
					bindInfoWindow(marker, map, infoWindow, marker_content);
				}
				setTimeout(function() {
					downloadUrl("Scripts/Refresh_xml.php", processXML);
				}, 20000); //Refresh every 20 seconds
			}
			
			
			function bindInfoWindow(marker, map, infoWindow, marker_content) {
				google.maps.event.addListener(marker, 'click', function() {
					infoWindow.setContent(marker_content);
					infoWindow.open(map, marker);

				/*	setTimeout(function() {
						downloadUrl("Scripts/Refresh_xml.php", processXML);
					}, 20000); //Refresh every 20 seconds */
				});

			}
			
			function resetMarkers(arr) {
				for (var i=0;i<arr.length; i++){
					arr[i].setMap(null);
				}
				//reset the main marker array for the next call
				arr=[];
			}
			
			function doNothing() {}
			
		</script>
	</head>
	<body onload="load()">
		
		<!-- Header -->
		<header id="header">
			<a href="#" class="image avatar"><img src="images/Windtrack.png" alt="" /></a>
			<h1><strong>Wind-Track</strong><br /><i>"Providing real-time user generated wind measurements"</i><br/></h1>
		</header>
		
		<!-- Main -->
		<div id="main">
			<a href="index_gr.php">
				<img src="images/el.gif" alt="Ελληνικά" title="Ελληνικά">
			</a>
			<a href="index.php">
				<img src="images/en.gif" alt="English" title="English">
			</a>
			<div id="map" style="width: 100%; height: 500px"></div>
			
			<!-- One -->
			<section id="one">
				<header class="major">
					<h2>How it works:</h2>
				</header>
				<p>The map shown above, depicts the latest wind measurements that were made in the past 20 seconds in the form of markers. At any time you may click on those markers to see additional info on the particular measurement. The timestamps of the measurements are in UTC.</p>
				<p>Wind-Track achieves real-time wind visualization with crowdsourcing. By using our <strong>"Wind-Track" application</strong> on your android smartphone, along with the wind meter provided from <strong>Weatherflow.com</strong>, you provide us with real-time data of the direction and velocity of the wind in your location. We receive that information and display it here for all to see.</p>
			</section>
			
			<!-- Two -->
			<section id="two">
				<header class="major">
					<h2>So what's the catch? Why do we do this?</h2>
				</header>
				<p>Sir Francis Bacon was attributed for his phrase "ipsa scientia potestas est", roughly translated to "Knowledge itself is power". Today, these words hold even more truth than ever.</p>
				<p>Our world consists of information, scattered around and waiting for someone to gather it and make use of it. Also, real-time information is scarce nowadays. The kind of information that most companies charge you for.</p>
				<p>That is what we're trying to achieve. Free, real-time data of wind measurements, without topological limitations and of course without harming the environment. When other companies charge you for the same services, we hand it out for free, provided that we have your continuous support.</p>
				<p>So, who knows, maybe one day this information will lead to something great. Maybe you get to see that your contribution helped make the world a "Greener" and better place. Until then, we can only continue measuring :)</p>
			</section>
			
			<!-- Three -->
			<section id="three">
				<header class="major">
					<h2>Interested in contributing?</h2>
				</header>
				<p>In order to provide our services efficiently, we rely solely on you, the user. So it's crucial that you keep supporting us :)</p>
				<p>How can you do that you ask? You will need the following:
					<li>An android smartphone device <strong>(Android Jellybean 4.3 or higher)</strong></li>
					<li>Download our free application "Wind-Track" from Google Play Store <strong>(Discontinued: 27/6/2016)</strong></li>
					<li>Get the Wind Meter for your smartphone, provided by <strong>Weatherflow.com</strong></li>
					<li>Plug the meter to your smartphone, and execute the application</li><br>
				Happy measuring !</p>
				<div class="row">
					<article class="6u 12u$(3)">
						
					</article>
					<article class="6u$ 12u$(3)">
						
					</article>
					<article class="6u 12u$(3)">
						
					</article>
				</div>
			</section>
			<!-- Four -->
			<section id="four">
				<header class="major">
					<h2>API Usage</h2>
				</header>
				<p>Our resources can be accessed through our API. Please refer to the table below, for more information on the API usage:</p>
				<table>
					<tr><td><p><strong>Definition</strong></p></td><td><p><strong>Example usage</strong></p></td></tr>
					<tr><td><p>Show all measurements (keyword: ALL)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack?q=ALL">http://giannispetridis.xyz/windtrack?q=ALL </a></p></td></tr>
					<tr><td><p>All measurements in xml format</p></td><td><p><a href="http://giannispetridis.xyz/windtrack/Scripts/Fetch_all.php" target="_blank">Data in XML </a></p></td></tr>
					<tr><td><p>Show all measurements made within days=5 days (provide integer, replace days=5)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack?days=5">http://giannispetridis.xyz/windtrack?days=5 </a></p></td></tr>
					<tr><td><p>All measurements made within days=5 days in xml format (provide integer, replace days=5)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack/Scripts/Data_to_xml.php?days=5" target="_blank">Measurements of q days in xml </a></p></td></tr>
					<tr><td><p>Show all measurements with wind velocity greater than velocity=5 (provide floating point number in MPH, replace velocity=5)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack?velocity=5">http://giannispetridis.xyz/windtrack?velocity=5</a></p></td></tr>
					<tr><td><p>All measurements with wind velocity greater than velocity=5 in xml (provide floating point number in MPH, replace velocity=5)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack/Scripts/Data_to_xml.php?velocity=5" target="_blank">Measurements with wind velocity greater than 5 </a></p></td></tr>
					<tr><td><p>Show all measurements with altitude greater than altitude=10 (provide floating point number, replace altitude=10)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack?altitude=5">http://giannispetridis.xyz/windtrack?altitude=5</a></p></td></tr>
					<tr><td><p>All measurements with altitude greater than altitude=10 in xml (provide floating point number, replace altitude=10)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack/Scripts/Data_to_xml.php?altitude=10" target="_blank">Measurements with altitude greater than 10 </a></p></td></tr>
					<tr><td><p>Show all measurements made by the user with name=testname (provide string, replace name=testname, works with either combination of first and last names)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack?name=testname">http://giannispetridis.xyz/windtrack?name=testname </a></p></td></tr>
					<tr><td><p>All measurements made by the user with name=testname in xml (provide string, replace name=testname, works with either combination of first and last names)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack/Scripts/Data_to_xml.php?name=testname" target="_blank">Measurements of user testname in xml</a></p></td></tr>
					<tr><td><p>Show all measurements made by the user with email=email@email.com(provide valid email, replace email = email@email.com)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack?email=email@email.com">http://giannispetridis.xyz/windtrack?email=email@email.com</a></p></td></tr>
					<tr><td><p>All measurements made by the user with email=email@email.com in xml (provide valid email, replace email = email@email.com)</p></td><td><p><a href="http://giannispetridis.xyz/windtrack/Scripts/Data_to_xml.php?email=email@email.com" target="_blank">Measurements of user with email: email@email.com in xml</a></p></td></tr>
					
				</table>
				<div class="row">
					<article class="6u 12u$(3)">
						
					</article>
					<article class="6u$ 12u$(3)">
						
					</article>
					<article class="6u 12u$(3)">
						
					</article>
				</div>
			</section>
			
			<!-- Five -->
			<section id="five">
				<h2>Contact us</h2>
				<p>Should you have any questions or want to get in touch with us regarding our work please feel free to contact us using the info provided:</p>
				<div class="4u$ 12u$(2)">
					<ul class="labeled-icons">
						<li>
							<h3 class="icon fa-home"><span class="label">Address></span></h3>
							-<br />
							
							
						</li>
						<li>
							<h3 class="icon fa-mobile"><span class="label">Phone</span></h3>
							-
						</li>
						<li>
							<h3 class="icon fa-envelope-o"><span class="label">Email</span></h3>
							<a href="mailto:mrjohnpetridis@gmail.com">mrjohnpetridis@gmail.com</a>
						</li>
					</ul>
				</div>
			</div>
		</section>
		
	</div>
	
	<!-- Footer -->
	<footer id="footer">
		<ul class="copyright">
			<li>&copy; Ioannis Petridis</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
		</ul>
	</footer>
	
</body>
</html>
