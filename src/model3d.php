<?php
// connect to DB
$pdo = new PDO('pgsql:host=db;port=5432;dbname=db;user=bkfast;password=bekfast');

$set = $pdo->query("select aircraft_data.*, x, y, z from aircraft_data join parts_mapping on parts_mapping.side = aircraft_data.side and parts_mapping.component = aircraft_data.component")->fetchAll();

// parse query output to create input array for canvasJS
$date_points = [];
foreach ($set as $res) {
    $color = NULL;
    switch ($res['anomaly'])
    {
        case 'Corrosão':
            $color = 0xff0000;
            break;
        case 'Fratura':
            $color = 0x0000ff;
            break;
        case 'Outro':
            $color = 0x00ff00;
            break;
    }
    $data_points[] = [
            'x' => floatval($res['x']),
            'y' => floatval($res['y']),
            'z' => floatval($res['z']),
            'color' => $color
    ];
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="media/normalize.css">

    <title>Breakfast Data!</title>
</head>
<body id="body">
    <script type="module">
        // Note issues described with import to update > 0.127
        // https://github.com/mrdoob/three.js/wiki/Migration-Guide#127--128

        import * as THREE from 'https://unpkg.com/three@0.127.0/build/three.module.js';
        import { OrbitControls } from 'https://unpkg.com/three@0.127.0/examples/jsm/controls/OrbitControls.js';
        import { OBJLoader } from 'https://unpkg.com/three@0.127.0/examples/jsm/loaders/OBJLoader.js';
        import { RoomEnvironment } from 'https://unpkg.com/three@0.127.0/examples/jsm/environments/RoomEnvironment.js';
        import GUI from 'https://cdn.jsdelivr.net/npm/lil-gui@0.16/+esm';

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // SETUP
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        const urlParams         = new URLSearchParams(window.location.search);
        const qsettings         = {
            'dot_noise':    urlParams.get('dot_noise')   ?? 1,           // Spheres will be placed randomly up to this*diameter of their original spot
            // Live update
            'dot_size':     urlParams.get('dot_size')    ?? 1,           // Action dots size
            'speed':        urlParams.get('speed')       ?? 0,           // Auto rotate plane speed
            'bg_color':     urlParams.get('bg_color')    ?? 0xaaaaaa,    // Background color
            'show_axes':    urlParams.get('show_axes')   ?? 0,           // Show coordinate axes
        }
        const loader            = new OBJLoader();
        const gui               = new GUI({title: 'Settings'});

        // Renderer
        const renderer          = new THREE.WebGLRenderer({ antialias: true });
        renderer.domElement.id  = 'yeet';
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight );
        document.body.appendChild(renderer.domElement);

        const pmremGenerator    = new THREE.PMREMGenerator(renderer);

        // Camera
        const camera            = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(75, 50, -50);

        // Orbit controls
        const controls          = new OrbitControls( camera, renderer.domElement );

        // Scene configs
        const scene             = new THREE.Scene();
        scene.background        = new THREE.Color(qsettings.bg_color);
        scene.environment       = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;

        const axes              = new THREE.AxesHelper(100);
        axes.visible            = false;

        scene.add(camera);
        scene.add(axes);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // LIGHTS
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        let ambient             = new THREE.AmbientLight(0x000000);
        scene.add(ambient);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // OBJECTS
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Group to hold all objects for coordinated rotation
        const plane_grp     = new THREE.Group();    // All that rotates in sync with the plane
        const points_grp    = new THREE.Group();    // Data points for easy iteration to resize / re-distribute

        // Load PLANE
        let plane;
        loader.load('media/plane787.obj', object => {
                // Extract mesh from obj & set material
                plane           = object.children[0];
                plane.material  = new THREE.MeshStandardMaterial({color: 0x049ef4, metalness: 1, roughness: 0.8});

                // Set initial properties & add to scene
                plane.position.set(0, 0, 0);
                plane.scale.set(1, 1, 1);
                plane_grp.add(plane);

                // Add GUI settings
                guiMeshStandardMaterial(gui, plane.material);
            }, undefined, error => console.error(error)
        );

        // Mouse click Sphere
        const sphere = new THREE.Mesh(new THREE.SphereGeometry(1, 32, 16), new THREE.MeshBasicMaterial({color: 0xffff00}));
        scene.add(sphere);

        // Action points
        let data        = <?= json_encode($data_points) ?>;
        const dot_size  = qsettings.dot_size;

        // positions are randomized to fall within up to <noise>% of the sphere's diameter
        let varpos      = (x, y, noise) => {
            let r = noise*dot_size*2 * Math.sqrt(Math.random());
            let a = Math.random() * 2 * Math.PI;
            return [x + r * Math.cos(a), y + r * Math.sin(a)];
        }

        data.forEach(point => {
            let sphere  = new THREE.Mesh(new THREE.SphereGeometry(1, 32, 16), new THREE.MeshBasicMaterial({color: point.color}));
            let [x, z]  = varpos(point.x, point.z,qsettings.dot_noise);

            sphere.scale.set(dot_size, dot_size, dot_size);
            sphere.position.set(x, point.y, z);
            points_grp.add(sphere);
        })

        plane_grp.add(points_grp);
        scene.add(plane_grp);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // GUI
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        const scene_gui     = gui.addFolder('Scene');
        const points_gui    = gui.addFolder('Data points');

        scene_gui.addColor(qsettings, 'bg_color')
            .onChange(value => {scene.background = new THREE.Color(value);})
            .name('Background color');
        scene_gui.add(axes, 'visible')
            .name('Show axes');

        points_gui.add(qsettings, 'dot_size', 0, 5)
            .onChange(value => {points_grp.children.forEach(dot => {dot.scale.set(value, value, value)})})
            .name('Dot size');

        function guiMeshStandardMaterial(gui, material) {
            const plane_gui = gui.addFolder('Plane model');
            plane_gui.add(material,'wireframe').name('Wireframe');
            plane_gui.add(qsettings,'speed', 0, 10).name('Rotation speed').listen();
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // MOUSE INTERSECT CALCULATION
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        let rayCaster       = new THREE.Raycaster();
        let mousePosition   = new THREE.Vector2();

        function onPointerDown(event) {
            event.preventDefault();
            mousePosition.x = (event.clientX / window.innerWidth) * 2 - 1;
            mousePosition.y = - (event.clientY / window.innerHeight) * 2 + 1;

            rayCaster.setFromCamera(mousePosition, camera);
            let intersects = rayCaster.intersectObjects([scene.getObjectByName('787')]);
            if (intersects.length)
            {
                let ip = intersects[0].point;
                sphere.position.set(ip.x, ip.y, ip.z);
                console.log(ip);
            }
            else
                console.log('no intersects');
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // EVENT LISTENERS
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        document.getElementById('yeet').addEventListener('dblclick', onPointerDown);
        window.addEventListener( 'resize', function () {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize( window.innerWidth, window.innerHeight );
        }, false );

        function animate() {
            requestAnimationFrame(animate);
            renderer.render(scene, camera);
            update();
        }

        function update() {
            controls.update();
            if(plane && qsettings.speed){
                let rotation_increment  = 0.001*qsettings.speed;
                plane_grp.rotation.y    = (plane_grp.rotation.y + rotation_increment) % (2*Math.PI);
                sphere.position.applyAxisAngle(new THREE.Vector3(0,1,0), rotation_increment);
            }
        }

        animate();
    </script>
</body>
</html>