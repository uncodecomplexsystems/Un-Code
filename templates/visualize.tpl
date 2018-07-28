{include file="head.internal.tpl" title="3D-Visualization / {$thisproject.project_name}"}

		<script src="include/three.min.js"></script>
		<script src="include/Detector.js"></script>
		<script src="include/OrbitControls.js"></script>
		<script src="fonts/helvetiker_regular.typeface.js"></script>
		<script src="include/THREE.Terrain.min.js"></script>
		
		<script>
		$(document).ready(function() {
		
			if (! Detector.webgl ) Detector.addGetWebGLMessage();
		
		function readyRender()
		{
			if ($('#timeslide_id').val() != null && $('#actor_id').val() != null && ($('#slopes').is(":checked") || $('#peaks').is(":checked") || $('#columns').is(":checked")) && ($('#greyscale').is(":checked") || $('#colors').is(":checked")) && ($('#psd_field').is(":checked") || $('#psd_case').is(":checked"))) return true;
			else return false;
		}
		
			$('#timeslide_id').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#actor_id').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#slopes').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#peaks').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#columns').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#greyscale').change(function(event) {
				if (readyRender()) visualize();
			});

			$('#colors').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#labels').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#arrows').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#transparency').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#highlight_id').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#highlight2_id').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#psd_field').change(function(event) {
				if (readyRender()) visualize();
			});
			
			$('#psd_case').change(function(event) {
				if (readyRender()) visualize();
			});

			
			{if isset($timeslide_id)}
			$('#timeslide_id option[value="{$timeslide_id}"]').prop("selected", true);
			{else}
			$("#timeslide_id").val($("#timeslide_id option:first").val());
			{/if}
			
			$('#actor_id option').prop('selected', true);
			$('#slopes').prop("checked", true);
			$('#greyscale').prop("checked", true);
			$('#labels').prop('checked', true);
			$('#transparency').prop('checked', true);
			$('#psd_field').prop('checked', true);

			visualize();
			
			var container;
			var camera, scene, renderer;
			var object, plane;
			var xAxis = new THREE.Vector3(1,0,0);
			var yAxis = new THREE.Vector3(0,1,0);
			var zAxis = new THREE.Vector3(0,0,1);
			var columnSize = 5;
			var gridSize = 100;
			var zoomFactor = 1.9;
			var i;
			var light, colorMat = [], greyMat = [], colorMatT = [], greyMatT = [], map, transparent, blankMat, gridMat;
			var response, dataArray;
			var columnCounter = 0, labelCounter = 0, texCounter = 0, x = 0, y = 0, z = 0, oldx = -1, oldy = -1, oldz = -1, prevx = -1, prevy = -1, prevz = -1, changeValue = 0;
			var columns = [], labels = [], walk = [], geo = [], direction = [], mat = [], slopes = [];
			var searching = false, found = false, moreEncounters = false;
			var heightmapImage;
			var getImageData = false;
	
			function rotateAroundWorldAxis( object, axis, radians )
			{
				var rotWorldMatrix;
				var rotationMatrix = new THREE.Matrix4();

				rotationMatrix.makeRotationAxis( axis.normalize(), radians );
				rotationMatrix.multiply( object.matrix );
				object.matrix = rotationMatrix;
				object.rotation.setFromRotationMatrix(object.matrix);
			}
			
			function open_data_uri_window(url)
			{
			   var url_with_name = url.replace("data:image/png;", "data:image/png;name=screenshot.png;")
			   var html = '<html>' +
				'<link rel="stylesheet" href="css/main.css" type="text/css">' +
				'<style>html, body { padding: 0; margin: 0; } iframe { width: 100%; height: 100%; border: 0;}  </style>' +
				'<body>' +
				'<h4>Please right-click on the screenshot below and choose "save as".</h4>' +
				'<h5 style="font-size: 60%;">If you do not see a screenshot below, you might be using Internet Explorer or Edge. Due to technical limitations of these browsers, taking screenshots via this method is not possible. Please go back to the visualization and instead use one of the following methods to take a screenshot:' +
				'<ol><li>Either use the Windows "Snipping Tool", built into every windows installation, which is accessible via the start menu.</li>' +
				'<li>Or  use the "Print Screen" button on your keyboard, open Paint and paste the screenshot via CTRL+V.</li>' +
				'<li>Or use a different browser, such as Chrome or Firefox.</ol></h5>' +
				'<iframe type="image/png" src="' + url_with_name + '"></iframe>' +
				'</body></html>';
				var a = window.open("about:blank", "Uncode_Screenshot");
				a.document.write(html);
				a.document.close();
			}
			
			// Draw columns and their labels
			function drawColumns()
			{
			
				function checkBorders(x, y)
				{
								var found = false;
								if ($('#slopes').is(':checked'))
								{
									if (x > 101 || x < 8 || y > 101 || y  < 8) found = true;
								}
								if ($('#columns').is(':checked'))
								{
									if (x > 102 || x < 6 || y > 102 || y  < 6) found = true;
								}
								if ($('#peaks').is(':checked'))
								{
									if (x > 100 || x < 7 || y > 100 || y  < 7) found = true;
								}
								if (found == false) return true; else return false;
				}
			
				geo = [];
				columnCounter = 0;
				labelCounter = 0;
				texCounter = 0;
				var actorCounter = 0;
				var currentTex, currentLabel;
				var starting_i = 2 * $("#timeslide_id :selected").length;
				var repoSize = columnSize;
				var repoGrid = [ [-repoSize, 0], [repoSize, 0], [0, -repoSize], [0, repoSize], [-repoSize, -repoSize], [repoSize, -repoSize], [-repoSize, repoSize], [repoSize, repoSize],
				[-1.5*repoSize, repoSize], [1.5*repoSize, repoSize], [repoSize, -1.5*repoSize], [repoSize, 1.5*repoSize], [-1.5*repoSize, -1.5*repoSize], [1.5*repoSize, -1.5*repoSize], [-1.5*repoSize, 1.5*repoSize], [1.5*repoSize, 1.5*repoSize]];
				
				for (i = starting_i; i < dataArray.length - starting_i; i+= (4+(($("#timeslide_id :selected").length-1)*3))) // Actors loop
				{
					oldx = -1;
					oldy = -1;
					oldz = -1;
					prevx = -1;
					prevy = -1;
					prevz = -1;
					actorCounter++;
					wasPresent = false;

					for (var t = 0; t < $("#timeslide_id :selected").length; t++) // Timeslide loop
					{

						if (dataArray[i+1+(t*3)] != 'not present') // If actor is present...
						{
							wasPresent = true;
					
							// Find out, if this is the actor's last present time slice. moreEncounters == true when there is at least one remaining existence
							moreEncounters = false;
							for (var j = t+1; j < $("#timeslide_id :selected").length; j++)
							{
								if (dataArray[i+1+(j*3)] != 'not present') moreEncounters = true;
							}
						
							// Set the right material for object and label
							if (moreEncounters && $('#transparency').is(':checked')) // Transparent materials
							{
								currentLabel = blankMatT;
								if ($('#highlight_id').val() != 0 || $('#highlight2_id').val() != 0) // Highlight-Function enabled
								{
									if ($('#greyscale').is(":checked"))
									{
										if (dataArray[i] == $('#highlight_id :selected').text()) currentTex = greyMatT[1]; // This is the highlighted stuff
										else if (dataArray[i] == $('#highlight2_id :selected').text()) currentTex = greyMatT[0]; // Highlight 2
										else currentTex = greyMatT[10];
									}
									else 
									{
										if (dataArray[i] == $('#highlight_id :selected').text()) currentTex = colorMatT[1]; // This is the highlighted stuff
										else if (dataArray[i] == $('#highlight2_id :selected').text()) currentTex = colorMatT[0]; // Highlight 2
										else currentTex = colorMatT[10];
									}
								}
								else // Normal stuff, without highlight function
								{
									if ($('#greyscale').is(":checked")) currentTex = greyMatT[texCounter]; else currentTex = colorMatT[texCounter];
								}
							}
							else // Non-transparent materials
							{
								currentLabel = blankMat;
								if ($('#highlight_id').val() != 0 || $('#highlight2_id').val() != 0)
								{
									if ($('#greyscale').is(":checked"))
									{
										if (dataArray[i] == $('#highlight_id :selected').text()) currentTex = greyMat[1];
										else if (dataArray[i] == $('#highlight2_id :selected').text()) currentTex = greyMat[0];
										else currentTex = greyMatT[10];
									}
									else
									{
										if (dataArray[i] == $('#highlight_id :selected').text()) currentTex = colorMat[1];
										else if (dataArray[i] == $('#highlight2_id :selected').text()) currentTex = colorMat[0];
										else currentTex = colorMatT[10];
									}
								}
								else
								{
									if ($('#greyscale').is(":checked")) currentTex = greyMat[texCounter]; else currentTex = colorMat[texCounter];
								}
							}
						
							// Adjustments for better visibility
							if ($('#slopes').is(':checked'))
							{
								x = dataArray[i+1+(t*3)]*gridSize+1;
								y = dataArray[i+2+(t*3)]*gridSize+1;
								if (x <= 7) x = 8;
								if (y <= 7) y = 8;
							}
							if ($('#columns').is(':checked'))
							{
								x = dataArray[i+1+(t*3)]*gridSize+2;
								y = dataArray[i+2+(t*3)]*gridSize+2;
								if (x <= 5) x = 6;
								if (y <= 5) y = 6;
							}
							if ($('#peaks').is(':checked'))
							{
								x = dataArray[i+1+(t*3)]*gridSize;
								y = dataArray[i+2+(t*3)]*gridSize;
								if (x <= 6) x = 7;
								if (y <= 6) y = 7;
							}
							
							z = dataArray[i+3+(t*3)]*gridSize;
							
							if ((oldx == x) && (oldy == y) && (oldz == z)) continue;
	
							oldx = x;
							oldy = y;
							oldz = z;

							// Check if a column overlaps with an existing one, if yes, then move it slightly (and recheck if there are again overlaps)
							if (columns.length > 0) // Only do this at the beginning of the 2nd column obviously
							{
								var repoCountA = 1;
								var repoCountB = 1;
								searching = true;
								var repetitions = 0;
								while (searching)
								{
									found = false;
									for (c = 0; c < columns.length; c++) // Compare with every other already existing column
									{
										if ((((x-(columnSize/2)) == columns[c].position.x) && ((y-(columnSize/2)) == columns[c].position.z)) || !checkBorders(x, y)) // Overlap with actor c or out of bounds?
										{
											found = true; // When found is set true, the search will be started from new again
										
											// Reverse to original position
											x = oldx;
											y = oldy;
										
											// Reposition algorithm
											x += (repoGrid[repoCountA-1][0]*repoCountB);
											y += (repoGrid[repoCountA-1][1]*repoCountB);

											repoCountA++;
											if (repoCountA > repoGrid.length)
											{
												repoCountA = 1;
												repoCountB++;
											}
										}
									}
									if (!found) searching = false;
									repetitions++;
									if (repoCountB > (110/columnSize)) // This is the limit how long the algorithm will try to find non-overlapping positions. If a huge number of actors stack together, there is no way to find a nearby unique place for everyone. Thus after 200 repetitions per actor, we will accept overlappings.
									{
										alert('a');
										searching = false;
										x = oldx;
										y = oldy;
									}
								}
							}
							
							if (prevx != -1)
							{
								// Render arrows if checked
								if ($('#arrows').is(':checked'))
								{
									var startPoint = new THREE.Vector3(prevx, prevz, prevy);
									var endPoint   = new THREE.Vector3(x,z,y);
									var continueArrow = true;
									if ( ( $('#highlight_id').val() != 0 || $('#highlight2_id').val() != 0 ) && (dataArray[i] != $('#highlight_id :selected').text() && dataArray[i] != $('#highlight2_id :selected').text())  ) continueArrow = false;
									
									if (continueArrow)
									{
										if (moreEncounters)
										{	// Draw a line as this is not the last existing position
											geo[geo.length] = new THREE.Geometry();
											geo[geo.length-1].vertices.push(startPoint, endPoint);
											geo[geo.length-1].computeLineDistances();
											if ($('#greyscale').is(":checked")) mat[mat.length] = new THREE.LineBasicMaterial( { color: 0x545454, linewidth: 2 } );
											else mat[mat.length] = new THREE.LineBasicMaterial( { color: 0xC00505, linewidth: 2 } );
											walk[walk.length] = new THREE.Line(geo[geo.length-1], mat[mat.length-1]);
											walk[walk.length-1].name = 'w'+(walk.length-1);
											scene.add(walk[walk.length-1]);
										}
										
										else
										{ // Draw an arrow as this is the last existing position
											direction[direction.length] = new THREE.Vector3().subVectors(endPoint, startPoint).normalize();
											if ($('#greyscale').is(":checked")) walk[walk.length] = new THREE.ArrowHelper(direction[direction.length-1], startPoint, startPoint.distanceTo(endPoint), 0x545454);
											else walk[walk.length] = new THREE.ArrowHelper(direction[direction.length-1], startPoint, startPoint.distanceTo(endPoint), 0xC00505);
											walk[walk.length-1].name = 'w'+(walk.length-1);
											scene.add(walk[walk.length-1]);
										}
									}
								}
							}
							
							prevx = x;
							prevy = y;
							prevz = z;
									
							// Slopes
							{literal}
							if ($('#slopes').is(':checked'))
							{
							
								var slopeXY = 5 + Math.floor(z / 25);
								if ((slopeXY % 2) == 0) slopeXY += 1;
								if (slopeXY == 5) var slopeRay = 1;

								slopes[slopes.length] = new THREE.Terrain({
									easing: THREE.Terrain.Linear,
									frequency: 2.5,
									heightmap: heightmapImage,
									material: currentTex,
									maxHeight: Math.round(z),
									minHeight: 0,
									steps: 1,
									useBufferGeometry: false,
									xSegments: 16,
									xSize: slopeXY,
									ySegments: 16,
									ySize: slopeXY,
									});
									
								slopes[slopes.length-1].position.set(Math.ceil(x-(slopeXY/2)), 0, Math.ceil(y-(slopeXY/2)));
								slopes[slopes.length-1].name = 's'+(slopes.length-1);
								scene.add(slopes[slopes.length-1]);
							}
							{/literal}

							// Peaks and Columns
							else if ($('#peaks').is(':checked')) geo[geo.length] = new THREE.CylinderGeometry(0, z/20, z, 12, 15, 50, false);
							else if ($('#columns').is(':checked')) geo[geo.length] = new THREE.BoxGeometry(columnSize, z, columnSize, 1, 10, 3);
							
							columns[columns.length] = new THREE.Mesh (geo[geo.length-1], currentTex);
							columns[columns.length-1].name = 'c'+columnCounter;
							columns[columns.length-1].position.set(x-(columnSize/2), z/2, y-(columnSize/2));
							columns[columns.length-1].castShadow = true;
							scene.add(columns[columns.length-1]);
							
							// Label of column
							if ($('#labels').is(':checked') && (($('#highlight_id').val() == 0 && $('#highlight2_id').val() == 0) || (($('#highlight_id').val() != 0 && dataArray[i] == $('#highlight_id :selected').text()) || ($('#highlight2_id').val() != 0 && dataArray[i] == $('#highlight2_id :selected').text()))))
							{
								if ($('#timeslide_id :selected').length > 1) labels[labels.length] = new THREE.Mesh( new THREE.TextGeometry(actorCounter+' (t'+(t+1)+')', { size: 4, height: 2, curveSegments: 2, font: "helvetiker" }), currentLabel );
								else labels[labels.length] = new THREE.Mesh( new THREE.TextGeometry(actorCounter, { size: 4, height: 2, curveSegments: 2, font: "helvetiker" }), currentLabel );
								labels[labels.length-1].name = 'l'+labelCounter;
								labels[labels.length-1].position.set(x-(columnSize/2)-2, z+5, y-(columnSize/2));
								scene.add(labels[labels.length-1]);
								labelCounter++;
							}
							
							columnCounter++;
						}
					}

					// Add legend entry
					if (($('#highlight_id').val() == 0) && ($('#highlight2_id').val () == 0)) // Normal procedure
					{
						if (wasPresent)
						{
							if ($('#greyscale').is(":checked")) $('#visualize_legend').append('<br>['+(actorCounter)+'] <img class="thumbnail" src="img/greyscale/'+texCounter+'.jpg"> '+dataArray[i]);
							else $('#visualize_legend').append('<br>['+(actorCounter)+'] <img class="thumbnail" src="img/colors/'+texCounter+'.jpg"> '+dataArray[i]);
						}
						else // No thumbnail if not present
						{
							$('#visualize_legend').append('<br>['+(actorCounter)+'] '+dataArray[i]+' <span id="smaller">(not present)</span>');
						}
					}
					else
					{
						if ($('#highlight_id').val() != 0)
						{
							if (dataArray[i] == $('#highlight_id :selected').text())
							{
								if (wasPresent)
								{
									if ($('#greyscale').is(":checked")) $('#visualize_legend').append('<br>['+(actorCounter)+'] <img class="thumbnail" src="img/greyscale/2.jpg"> '+dataArray[i]);
									else $('#visualize_legend').append('<br>['+(actorCounter)+'] <img class="thumbnail" src="img/colors/1.jpg"> '+dataArray[i]);
								}
								else // No thumbnail if not present
								{
									$('#visualize_legend').append('<br>['+(actorCounter)+'] '+dataArray[i]+' <span id="smaller">(not present)</span>');
								}
							}
						}
						if ($('#highlight2_id').val() != 0)
						{
							if (dataArray[i] == $('#highlight2_id :selected').text())
							{
								if (wasPresent)
								{
									if ($('#greyscale').is(":checked")) $('#visualize_legend').append('<br>['+(actorCounter)+'] <img class="thumbnail" src="img/greyscale/4.jpg"> '+dataArray[i]);
									else $('#visualize_legend').append('<br>['+(actorCounter)+'] <img class="thumbnail" src="img/colors/0.jpg"> '+dataArray[i]);
								}
								else // No thumbnail if not present
								{
									$('#visualize_legend').append('<br>['+(actorCounter)+'] '+dataArray[i]+' <span id="smaller">(not present)</span>');
								}
							}
						}
					}

					if (wasPresent) texCounter++;
					if (texCounter > 19) texCounter = 0;
				}
			}
			
	
			function init() {
				
				container = document.createElement( 'div' );
				container.className = 'visualize';
				document.body.appendChild( container );
				scene = new THREE.Scene();
				
				// Light
				scene.add( new THREE.AmbientLight( 0xBABABA ) );
				light = new THREE.DirectionalLight( 0xffffff );
				light.intensity = 0.5;
				light.position.set(90, 150, 180);
				light.castShadow = true;
				light.shadowMapWidth = 512;
				light.shadowMapHeight = 512;
				var d = 100;
				light.shadowCameraLeft = -d;
				light.shadowCameraRight = d;
				light.shadowCameraTop = d;
				light.shadowCameraBottom = -d;
				light.shadowCameraFar = 1000;
				light.shadowDarkness = 0.35;
				scene.add( light );

				// Textures general
				heightmapImage = new Image();
				heightmapImage.src = 'img/slope.png';	
								
				map = THREE.ImageUtils.loadTexture( 'img/blank.png' );
				map.wrapS = map.wrapT = THREE.ClampToEdgeWrapping;
				map.minFilter = THREE.LinearFilter;
				map.anisotropy = 16;
				blankMat = new THREE.MeshLambertMaterial( { map: map, side: THREE.DoubleSide } );
				blankMatT = new THREE.MeshLambertMaterial( { map: map, side: THREE.DoubleSide, transparent: true, opacity: 0.3 } );
				
				map = THREE.ImageUtils.loadTexture( 'img/grid.png' );
				map.wrapS = map.wrapT = THREE.ClampToEdgeWrapping;
				map.minFilter = THREE.LinearFilter;
				map.anisotropy = 16;
				gridMat = new THREE.MeshLambertMaterial( { map: map, side: THREE.DoubleSide } );

				// Textures grey scale
				for (var c = 0; c < 20; c++)
				{
					if ($('#greyscale').is(":checked")) map = THREE.ImageUtils.loadTexture( 'img/greyscale/'+c+'.jpg' );
					else map = THREE.ImageUtils.loadTexture( 'img/colors/'+c+'.jpg' );
					map.wrapS = map.wrapT = THREE.ClampToEdgeWrapping;
					map.minFilter = THREE.LinearFilter;
					map.anisotropy = 16;
					greyMat.push(new THREE.MeshPhongMaterial( { map: map, side: THREE.DoubleSide } ));
					greyMatT.push(new THREE.MeshPhongMaterial( { map: map, side: THREE.DoubleSide, transparent: true, opacity: 0.4 } ));
				}
				
				// Textures color
				for (var c = 0; c < 20; c++)
				{
					map = THREE.ImageUtils.loadTexture( 'img/colors/'+c+'.jpg' );
					map.wrapS = map.wrapT = THREE.ClampToEdgeWrapping;
					map.minFilter = THREE.LinearFilter;
					map.anisotropy = 16;
					colorMat.push(new THREE.MeshPhongMaterial( { map: map, side: THREE.DoubleSide } ));
					colorMatT.push(new THREE.MeshPhongMaterial( { map: map, side: THREE.DoubleSide, transparent: true, opacity: 0.4 } ));
				}
				
				// Coordinate Axis
				axis = new THREE.AxisHelper(gridSize+(columnSize/2));
				axis.position.set( 0, 0, 0 );
				scene.add(axis);
				
				// Grid
				plane = new THREE.Mesh( new THREE.PlaneBufferGeometry( gridSize+(columnSize/2), gridSize+(columnSize/2) ), gridMat );
				plane.position.set( (gridSize+(columnSize/2))/2, -0.1, (gridSize+(columnSize/2))/2 );
				rotateAroundWorldAxis(plane, xAxis, -Math.PI/2);
				plane.receiveShadow = true;
				scene.add( plane );
				
				// Labels for Axis
				object = new THREE.Mesh( new THREE.TextGeometry('PSD', { size: 4, height: 1, curveSegments: 2, font: "helvetiker" }), blankMat );
				object.name = 'a1';
				object.position.set(5+gridSize+(columnSize/2), 0, 0);
				scene.add( object );
				object = new THREE.Mesh( new THREE.TextGeometry('CON', { size: 4, height: 1, curveSegments: 2, font: "helvetiker" }), blankMat );
				object.name = 'a2';
				object.position.set(-12.5, 0, 15+gridSize+(columnSize/2));
				scene.add( object );
				object = new THREE.Mesh( new THREE.TextGeometry('FIT', { size: 4, height: 1, curveSegments: 2, font: "helvetiker" }), blankMat );
				object.name = 'a3';
				object.position.set(-5, 5+gridSize+(columnSize/2), 0);
				scene.add( object );
				
				// Numbers for Axis
				for (var a = 1; a <= 10; a++) // PSD
				{
					object = new THREE.Mesh( new THREE.TextGeometry(a/10, { size: 3, height: 0.5, curveSegments: 2, font: "helvetiker" }), blankMat );
					object.name = 'axis_x'+a;
					object.position.set(a*10, 2, -2);
					scene.add( object );
				}
				for (var a = 1; a <= 10; a++) // C-Score
				{
					object = new THREE.Mesh( new THREE.TextGeometry(a/10, { size: 3, height: 0.5, curveSegments: 2, font: "helvetiker" }), blankMat );
					object.name = 'axis_y'+a;
					object.position.set(-8, 0, a*10+5);
					scene.add( object );
				}
				for (var a = 1; a <= 10; a++) // Fitness
				{
					object = new THREE.Mesh( new THREE.TextGeometry(a/10, { size: 3, height: 0.5, curveSegments: 2, font: "helvetiker" }), blankMat );
					object.name = 'axis_z'+a;
					object.position.set(2, a*10, 0);
					scene.add( object );
				}
				
				// Draw Columns and Labels
				drawColumns();
					
				// Renderer
				renderer = new THREE.WebGLRenderer( { antialias: true, alpha: true, preserveDrawingBuffer: true } );
				renderer.setClearColor( 0xffffff, 1 );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth*0.75, window.innerHeight*0.75 );
				renderer.shadowMapEnabled = true;
				renderer.shadowMapSoft = true;
				renderer.shadowMapType = THREE.PCFSoftShadowMap;
				container.appendChild( renderer.domElement );
				window.addEventListener( 'resize', onWindowResize, false );
				
				// Camera
				camera = new THREE.PerspectiveCamera( 50, window.innerWidth / window.innerHeight, 1, 2000 );
				camera.position.x = 90*zoomFactor;
				camera.position.y = 70*zoomFactor;
				camera.position.z = 90*zoomFactor;
		        controls = new THREE.OrbitControls( camera, renderer.domElement );
				controls.addEventListener( 'change', render );
				
				// Hotkeys
				$(document).keydown(function(e) {
				switch(e.which) {
					case 83: // s = take screenshot, will be handled in renderer()
						getImageData = true;
					break;
					
					case 79: // o = original camera position
						camera.position.x = 90*zoomFactor;
						camera.position.y = 70*zoomFactor;
						camera.position.z = 90*zoomFactor;
					break;
					
					case 78: // n = next time slice
						var newIndex = $('#timeslide_id :selected').next().index();
						if (newIndex != -1)	$("#timeslide_id").prop('selectedIndex', newIndex); else $("#timeslide_id").prop('selectedIndex', 0)
						if (readyRender()) visualize()
					break;
					
					case 66: // b = previous time slice
						var newIndex = $('#timeslide_id :selected').prev().index();
						if (newIndex != -1)	$("#timeslide_id").prop('selectedIndex', newIndex); else $("#timeslide_id").prop('selectedIndex', $('#timeslide_id option').length-1)
						if (readyRender()) visualize()
					break;

					default: return;
				}
				e.preventDefault();
			});

			}

			function onWindowResize() {

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth*0.7, window.innerHeight*0.7 );

			}

			function alignLabels()
			{
				scene.getObjectByName('a1').lookAt( camera.position );
				scene.getObjectByName('a2').lookAt( camera.position );
				scene.getObjectByName('a3').lookAt( camera.position );
			
				if ($('#labels').is(':checked')) for (i = 0; i < labels.length; i++)
				{
					scene.getObjectByName('l'+i).lookAt( camera.position );
				}
				
				for (var i = 1; i <= 10; i++)
				{
					scene.getObjectByName('axis_x'+i).lookAt( camera.position );
				}
				for (var i = 1; i <= 10; i++)
				{
					scene.getObjectByName('axis_y'+i).lookAt( camera.position );
				}
				for (var i = 1; i <= 10; i++)
				{
					scene.getObjectByName('axis_z'+i).lookAt( camera.position );
				}	
			}
			
			function animate()
			{
				alignLabels();
				
				var pLocal = new THREE.Vector3( 0, 0, -1 );
				var pWorld = pLocal.applyMatrix4( camera.matrixWorld );
				var dir = pWorld.sub( camera.position ).normalize();

				requestAnimationFrame( animate );
				render();
			}
			
			function render()
			{
				camera.lookAt( scene.position );
				renderer.render( scene, camera );
				
				if(getImageData == true) {
					window.setTimeout(function () {
					open_data_uri_window(renderer.domElement.toDataURL("image/png", 1.0));
					}, 100);
					getImageData = false;
				}		
			}
			
		function visualize()
		{
			// AJAX
			if (window.XMLHttpRequest)
			{
				// IE7+, Chrome, Firefox, Safari, Opera
				xmlhttp=new XMLHttpRequest();
			}
			else
			{
				// IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					document.getElementById("loading").style="visibility: hidden;";
					response = xmlhttp.responseText;
					dataArray = response.split('^');
					
					if (dataArray.length < 2)
					{
						alert('Error: '+response);
						return -1;
					}
					
					// New scene
					if (scene == null)
					{
						init();
						animate();
					}
					
					// Existing scene? First delete and clean up old stuff
					else
					{
						for (i = 0; i < columns.length; i++)
						{
							scene.remove(scene.getObjectByName('c'+i));
							columns[i].geometry.dispose();
							columns[i].geometry = null;
							columns[i] = null;
						}

						for (i = 0; i < labels.length; i++)
						{
							scene.remove(scene.getObjectByName('l'+i));
							labels[i].geometry.dispose();
							labels[i].geometry = null;
							labels[i] = null;
						}
						
						for (i = 0; i < slopes.length; i++)
						{
							scene.remove(scene.getObjectByName('s'+i));
							slopes[i] = null;
						}
						
						for (i = 0; i < walk.length; i++)
						{
							scene.remove(scene.getObjectByName('w'+i));
							walk[i] = null;
						}
						
						for (i = 0; i < geo.length; i++)
						{
							geo[i].dispose();
							geo[i] = null;
						}
						
						for (i = 0; i < direction.length; i++)
						{
							direction[i] = null;
						}
						
						for (i = 0; i < mat.length; i++)
						{
							mat[i].dispose();
							mat[i] = null;
						}
	
						columns = [];
						slopes = [];
						labels = [];
						walk = [];
						direction = [];
						mat = [];
						$('#visualize_legend').empty();
						
						drawColumns();
						animate();
					}
				}
			}
			
			var numTimeslides = 0;
			var timeslides = '';
			var el = document.getElementById('timeslide_id');
			for (var i = 0; i < el.options.length; i++) {
				if (el.options[i].selected) {
					numTimeslides++;
					timeslides = timeslides+'&timeslide'+numTimeslides+'_id='+el.options[i].value;
				}
			}
			
			var numActors = 0;
			var actors = '';
			var el = document.getElementById('actor_id');
			for (var i = 0; i < el.options.length; i++) {
				if (el.options[i].selected) {
					numActors++;
					actors = actors+'&actor'+numActors+'_id='+el.options[i].value;
				}
			}
			
			if ($('#psd_case').is(":checked")) xmlhttp.open("GET","?p=visualizedata&project_id={$thisproject.project_id}&psd_case=1&num_timeslides="+numTimeslides+"&num_actors="+numActors+timeslides+actors,true);
			else xmlhttp.open("GET","?p=visualizedata&project_id={$thisproject.project_id}&num_timeslides="+numTimeslides+"&num_actors="+numActors+timeslides+actors,true);
			document.getElementById("loading").style="visibility: visible;";
			xmlhttp.send();
		}
		
});
</script>

<h3>&nbsp; 3D-Visualization of {$thisproject.project_name}</h3>
<div id="visualize_legend"></div>

<div id="visualize_navigation">

<table id="visualize_timeslide"><tr><td>
<h4 style="text-align: center;">Fields in this case</h4>
<select multiple name="timeslide_id" id="timeslide_id" class="biglist" size="6">
{$counter = 1}
{foreach $timeslides as $row}
<option value="{$row.timeslide_id}">({$counter}) {$row.timeslide_name}</option>
{$counter++}
{/foreach}
</select>
<span class="middle" id="smaller">Select one or more Fields.</span>
</td></tr></table>
<table id="visualize_actor"><tr><td>
<h4 style="text-align: center;">Actors in this case</h4>
<select multiple name="actor_id" id="actor_id" class="biglist" size="6">
{foreach $actors as $row}
<option value="{$row.actor_id}">{$row.actor_name}</option>
{/foreach}
</select>
<span class="middle" id="smaller">Select one or more Actors.</span>
</td></tr></table>
<table style="width:280px" id="visualize_actor"><tr><td>
<h4 style="text-align: center;">PSD relative to...</h4>
<span id="smaller" class="middle">
<input type="radio" id="psd_field" name="psd_vis"> Single Field &nbsp; &nbsp; 
<input type="radio" id="psd_case" name="psd_vis"> Whole Case
</span></td></tr><tr><td>
<h4 style="text-align: center;">Visualization Method</h4>
<span id="smaller" class="middle">
<input type="radio" id="slopes" name="method"> Slopes &nbsp; &nbsp; 
<input type="radio" id="peaks" name="method"> Peaks &nbsp; &nbsp; 
<input type="radio" id="columns" name="method"> Columns
</span></td></tr><tr><td>
<h4 style="text-align: center;">Display Options</h4>
<span id="smaller" class="middle">
<input type="radio" id="greyscale" name="display"> Grey-Scale &nbsp; &nbsp; 
<input type="radio" id="colors" name="display"> Colors &nbsp; &nbsp; 
<input type="checkbox" id="labels">Show Labels
<br>
Highlight 1
<select name="highlight_id" id="highlight_id" class="smalllist" size="1">
<option value="0">[none]</option>
{foreach $actors as $row}
<option value="{$row.actor_id}">{$row.actor_name}</option>
{/foreach}
</select>
 &nbsp; Highlight 2
<select name="highlight2_id" id="highlight2_id" class="smalllist" size="1">
<option value="0">[none]</option>
{foreach $actors as $row}
<option value="{$row.actor_id}">{$row.actor_name}</option>
{/foreach}
</select>
</span></td></tr><tr><td>
<h4 style="text-align: center;">Adaptive Walk</h4>
<span id="smaller" class="middle">
<input type="checkbox" id="arrows">Show movement &nbsp; 
<input type="checkbox" id="transparency">Transparent past
</span></td></tr></table>

</div>

<div class="bottom"><div class="explanation">Drag mouse to <b>rotate</b>. Use mouse wheel to <b>zoom</b>. Press 'o' for <b>original camera</b> position.<br>Use 's' key to take a <b>screenshot</b> in a new tab. Press 'n' for the <b>next</b> Field and 'b' for the <b>previous</b> one.</div></div>
<img id="loading" src='img/loading.gif'>
<input id="visualize_return" type="button" value="Edit Lineage" onclick="location.href='?p=project&project_id={$thisproject.project_id}'">
<img src="img/slope.png" style="display: none;">
		
{include file="foot.tpl"}