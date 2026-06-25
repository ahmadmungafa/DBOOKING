<?php
require_once 'config/database.php';

// Handle booking submission
$booking_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $guests = $_POST['guests'];
    $total_price = $_POST['total_price'];
    $booking_code = 'DB' . strtoupper(uniqid());
    
    $data = [
        ':property_id' => $property_id,
        ':customer_name' => $customer_name,
        ':customer_email' => $customer_email,
        ':customer_phone' => $customer_phone,
        ':check_in' => $check_in,
        ':check_out' => $check_out,
        ':guests' => $guests,
        ':total_price' => $total_price,
        ':booking_code' => $booking_code
    ];
    
    try {
        $booking_id = createBooking($data);
        if($booking_id) {
            $booking_success = true;
        }
    } catch(Exception $e) {
        $error_message = "Gagal melakukan booking: " . $e->getMessage();
    }
}

// Get all properties
$villas = getProperties('villa', 6);
$homestays = getProperties('homestay', 6);
$shuttles = getProperties('shuttle', 4);
$jeeps = getProperties('jeep', 4);

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1586444248902-2f64eddc13df?ixlib=rb-4.0.3') center/cover; height: 500px;">
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="text-center text-white">
            <h1 class="display-3 fw-bold">Pesan Akomodasi & Transportasi di Dieng</h1>
            <p class="lead">Villa, Homestay, Shuttle, dan Jeep Wisata - Harga Terbaik!</p>
            <div class="search-box bg-white p-4 rounded-4 mt-4" style="max-width: 800px; margin: 0 auto;">
                <form action="#villa" method="get" class="row g-3">
                    <div class="col-md-5">
                        <input type="date" class="form-control" name="check_in" required>
                    </div>
                    <div class="col-md-5">
                        <input type="date" class="form-control" name="check_out" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <!-- Alert Messages -->
    <?php if($booking_success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> Booking berhasil! Kode booking Anda: <?php echo $booking_code; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Villa Section -->
    <section id="villa" class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-home text-primary"></i> Villa Premium</h2>
            <a href="#" class="text-primary">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row">
            <?php foreach($villas as $villa): ?>
            <div class="col-md-4 col-lg-4 mb-4">
                <div class="card property-card h-100 shadow-sm">
                    <img src="<?php echo $villa['image']; ?>" class="card-img-top" alt="<?php echo $villa['name']; ?>" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $villa['name']; ?></h5>
                        <p class="card-text text-muted"><?php echo substr($villa['description'], 0, 100); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-bed"></i> <?php echo $villa['bedrooms']; ?> Kamar
                                <span class="ms-2"><i class="fas fa-users"></i> <?php echo $villa['max_guests']; ?> Org</span>
                            </div>
                            <h5 class="text-primary mb-0">Rp <?php echo number_format($villa['price'], 0, ',', '.'); ?><small>/malam</small></h5>
                        </div>
                        <button class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#bookingModal" data-property='<?php echo json_encode($villa); ?>'>
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Homestay Section -->
    <section id="homestay" class="mb-5">
        <h2 class="mb-4"><i class="fas fa-hotel text-success"></i> Homestay Nyaman</h2>
        <div class="row">
            <?php foreach($homestays as $homestay): ?>
            <div class="col-md-4 mb-4">
                <div class="card property-card h-100 shadow-sm">
                    <img src="<?php echo $homestay['image']; ?>" class="card-img-top" alt="<?php echo $homestay['name']; ?>" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $homestay['name']; ?></h5>
                        <p class="card-text text-muted"><?php echo substr($homestay['description'], 0, 100); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-bed"></i> <?php echo $homestay['bedrooms']; ?> Kamar
                                <span class="ms-2"><i class="fas fa-users"></i> <?php echo $homestay['max_guests']; ?> Org</span>
                            </div>
                            <h5 class="text-primary mb-0">Rp <?php echo number_format($homestay['price'], 0, ',', '.'); ?><small>/malam</small></h5>
                        </div>
                        <button class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#bookingModal" data-property='<?php echo json_encode($homestay); ?>'>
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Shuttle & Jeep Section -->
    <div class="row">
        <div class="col-md-6">
            <section id="shuttle" class="mb-5">
                <h2 class="mb-4"><i class="fas fa-bus text-info"></i> Shuttle Transport</h2>
                <div class="row">
                    <?php foreach($shuttles as $shuttle): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card property-card shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?php echo $shuttle['image']; ?>" class="img-fluid rounded-start" style="height: 150px; width: 100%; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $shuttle['name']; ?></h5>
                                        <p class="card-text"><?php echo $shuttle['description']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-users"></i> Max <?php echo $shuttle['max_guests']; ?> orang</span>
                                            <h5 class="text-primary mb-0">Rp <?php echo number_format($shuttle['price'], 0, ',', '.'); ?><small>/hari</small></h5>
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#bookingModal" data-property='<?php echo json_encode($shuttle); ?>'>
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
        
        <div class="col-md-6">
            <section id="jeep" class="mb-5">
                <h2 class="mb-4"><i class="fas fa-car text-warning"></i> Jeep Wisata</h2>
                <div class="row">
                    <?php foreach($jeeps as $jeep): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card property-card shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="<?php echo $jeep['image']; ?>" class="img-fluid rounded-start" style="height: 150px; width: 100%; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $jeep['name']; ?></h5>
                                        <p class="card-text"><?php echo $jeep['description']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-users"></i> Max <?php echo $jeep['max_guests']; ?> orang</span>
                                            <h5 class="text-primary mb-0">Rp <?php echo number_format($jeep['price'], 0, ',', '.'); ?><small>/paket</small></h5>
                                        </div>
                                        <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#bookingModal" data-property='<?php echo json_encode($jeep); ?>'>
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-check"></i> Form Booking</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="bookingForm">
                <div class="modal-body">
                    <input type="hidden" name="property_id" id="property_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="customer_email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="customer_phone" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Check In</label>
                                <input type="date" name="check_in" class="form-control" required id="check_in">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Check Out</label>
                                <input type="date" name="check_out" class="form-control" required id="check_out">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Tamu</label>
                                <input type="number" name="guests" class="form-control" min="1" required id="guests">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detail Properti</label>
                        <div id="property_detail" class="p-3 bg-light rounded"></div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Total Harga:</strong> <span id="total_price_display">Rp 0</span>
                        <input type="hidden" name="total_price" id="total_price">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript untuk modal booking
document.addEventListener('DOMContentLoaded', function() {
    const bookingModal = document.getElementById('bookingModal');
    let currentProperty = null;
    
    bookingModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        currentProperty = JSON.parse(button.getAttribute('data-property'));
        
        document.getElementById('property_id').value = currentProperty.id;
        
        const propertyDetail = `
            <strong>${currentProperty.name}</strong><br>
            ${currentProperty.description}<br>
            <strong>Harga:</strong> Rp ${new Intl.NumberFormat('id-ID').format(currentProperty.price)}/${currentProperty.type === 'villa' || currentProperty.type === 'homestay' ? 'malam' : (currentProperty.type === 'shuttle' ? 'hari' : 'paket')}
        `;
        document.getElementById('property_detail').innerHTML = propertyDetail;
        
        // Reset form
        document.getElementById('check_in').value = '';
        document.getElementById('check_out').value = '';
        document.getElementById('guests').value = '1';
        updateTotalPrice();
    });
    
    function updateTotalPrice() {
        if(currentProperty) {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            let totalPrice = currentProperty.price;
            
            if(currentProperty.type === 'villa' || currentProperty.type === 'homestay') {
                if(checkIn && checkOut) {
                    const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                    if(nights > 0) {
                        totalPrice = currentProperty.price * nights;
                    }
                }
            }
            
            document.getElementById('total_price_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPrice);
            document.getElementById('total_price').value = totalPrice;
        }
    }
    
    document.getElementById('check_in').addEventListener('change', updateTotalPrice);
    document.getElementById('check_out').addEventListener('change', updateTotalPrice);
});
</script>

<?php include 'includes/footer.php'; ?>