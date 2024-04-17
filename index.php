<?php
// Include the QRCode library
use chillerlan\QRCode\{QRCode, QROptions};
require_once __DIR__.'/vendor/autoload.php';

// Function to generate QR code
function generateQRCode($data) {
    // Set QR code options
    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_H,
        'imageBase64' => false,
    ]);

    // Generate QR code
    $qrcode = (new QRCode($options))->render($data);

    return $qrcode;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $data = $_POST['qrData'];

    // Generate QR code
    $qrcode = generateQRCode($data);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Cowboy Bebop -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cowboy+Bebop&display=swap">
    <!-- Three.js -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.134.0/build/three.min.js"></script>
    <style>
    /* Custom CSS for Cowboy Bebop theme */
    body {
        margin: 0;
        padding: 0;
        font-family: 'Cowboy Bebop', cursive;
        /* Apply Cowboy Bebop font */
        overflow: hidden;
    }

    #canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    #info {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 10px;
        border-radius: 5px;
        display: none;
    }

    .container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        max-width: 500px;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        font-size: 32px;
        margin-bottom: 20px;
        color: #ff6699;
    }

    .form-label {
        font-weight: bold;
        color: #3366ff;
    }

    .form-control {
        border-radius: 5px;
        border: 2px solid #3366ff;
        background-color: #ffffff;
        color: #000000;
    }

    .btn-primary {
        border-radius: 25px;
        width: 100%;
        padding: 10px 20px;
        background-color: #ff6699;
        border: none;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #ff3366;
    }

    .qr-code {
        max-width: 100%;
        height: auto;
        margin-top: 20px;
    }

    .download-link {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 10px;
        background-color: #3366ff;
        color: #ffffff;
        text-decoration: none;
        border-radius: 25px;
        transition: background-color 0.3s;
    }

    .download-link:hover {
        background-color: #003366;
    }

    .download-icon {
        margin-right: 5px;
    }
    </style>
</head>

<body>
    <canvas id="canvas"></canvas>
    <div class="container">
        <h1>QR Code Generator</h1>
        <form method="post">
            <div class="mb-3">
                <label for="qrData" class="form-label">Enter text or link:</label>
                <input type="text" class="form-control" id="qrData" name="qrData"
                    value="<?php if(isset($data)){ echo htmlspecialchars($data); } ?>" placeholder="Enter text or link">
            </div>
            <button type="submit" class="btn btn-primary">Generate QR Code</button>
        </form>

        <!-- Display QR code with logo -->
        <?php if (isset($qrcode)): ?>
        <div class="text-center">
            <img src="data:image/png;base64,<?php echo base64_encode($qrcode); ?>" class="qr-code" alt="QR Code">
            <a href="data:image/png;base64,<?php echo base64_encode($qrcode); ?>" download="qrcode.png"
                class="download-link mt-3">
                <i class="fas fa-download download-icon"></i> Download QR Code
            </a>
        </div>
        <?php endif; ?>
    </div>
    <script>
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const starsGeometry = new THREE.BufferGeometry();
    const starsVertices = [];

    for (let i = 0; i < 500; i++) {
        const x = (Math.random() - 0.5) * 2000;
        const y = (Math.random() - 0.5) * 2000;
        const z = (Math.random() - 0.5) * 2000;
        starsVertices.push(x, y, z);
    }

    starsGeometry.setAttribute('position', new THREE.Float32BufferAttribute(starsVertices, 3));

    const starMaterial = new THREE.PointsMaterial({
        color: 0xffffff,
        size: 2,
    });

    const stars = new THREE.Points(starsGeometry, starMaterial);
    scene.add(stars);

    camera.position.z = 100;

    let isDragging = false;
    let previousMousePosition = {
        x: 0,
        y: 0
    };

    function handleMouseDown(event) {
        isDragging = true;
        previousMousePosition = {
            x: event.clientX,
            y: event.clientY
        };
    }

    function handleMouseMove(event) {
        if (!isDragging) return;

        const deltaMove = {
            x: event.clientX - previousMousePosition.x,
            y: event.clientY - previousMousePosition.y
        };

        const deltaRotationQuaternion = new THREE.Quaternion()
            .setFromEuler(new THREE.Euler(
                toRadians(deltaMove.y * 0.5),
                toRadians(deltaMove.x * 0.5),
                0,
                'XYZ'
            ));

        camera.quaternion.multiplyQuaternions(deltaRotationQuaternion, camera.quaternion);

        previousMousePosition = {
            x: event.clientX,
            y: event.clientY
        };
    }

    function handleMouseUp() {
        isDragging = false;
    }

    function animateStars() {
        if (!isDragging) {
            stars.rotation.x += 0.001;
            stars.rotation.y += 0.001;
            stars.rotation.z += 0.001;

            for (let i = 0; i < starsGeometry.attributes.position.array.length; i += 3) {
                starsGeometry.attributes.position.array[i] += Math.random() - 0.5;
                starsGeometry.attributes.position.array[i + 1] += Math.random() - 0.5;
                starsGeometry.attributes.position.array[i + 2] += Math.random() - 0.5;
            }

            starsGeometry.attributes.position.needsUpdate = true;
        }

        renderer.render(scene, camera);
        requestAnimationFrame(animateStars);
    }

    function toRadians(degrees) {
        return degrees * Math.PI / 180;
    }

    document.addEventListener('mousedown', handleMouseDown);
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);

    animateStars();
    </script>
</body>

</html>