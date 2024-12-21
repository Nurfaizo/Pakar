<?php
// Sistem Pakar dengan Forward Chaining dan Bootstrap

// Data Gejala dan Kerusakan
$gejala = [
    'p1' => 'Kekuatan Mesin lemah',
    'p2' => 'Suara mesin yang kasar',
    'p3' => 'Knalpot mengeluarkan asap putih',
    'p4' => 'Kompresi di bawah standar',
    'p5' => 'Boros Minyak Oli',
    'p6' => 'Piston Tergores',
    'p7' => 'Penurunan Akselerasi',
    'p8' => 'Terjadi Slip Pada Mesin',
    'p9' => 'Tidak Bisa Berjalan',
    'p10' => 'Mesin Melambat pada saat berjalan',
    'p11' => 'Api Busi Merah Kecil',
    'p12' => 'Mesin Mati Total',
    'p13' => 'Pemborosan bahan bakar',
    'p14' => 'Penurunan Kompresi',
    'p15' => 'Mesin tidak stabil',
    'p16' => 'Asap hitam keluar dari knalpot',
    'p17' => 'Pembekokan pada Klep',
    'p18' => 'Mesin terdengar kasar',
    'p19' => 'Kurangnya gaya pengereman',
    'p20' => 'Pengereman tidak berfungsi',
    'p21' => 'Lengket pada Rem',
    'p22' => 'Mesin tidak dapat dihidupkan dengan starter listrik',
    'p23' => 'Suara kasar saat menggunakan starter elektrik',
    'p24' => 'Generator mulai panas',
    'p25' => 'Saluran minyak Oli tidak beroperasi',
    'p26' => 'Mesin terasa panas berlebihan',
    'p27' => 'Minyak oli banyak ke bak mesin',
    'p28' => 'Terjadi Jim pada mesin'
];

$kerusakan = [
    'k1' => 'Piston Rusak',
    'k2' => 'Van Belt Rusak',
    'k3' => 'CDI Rusak',
    'k4' => 'Klep Rusak',
    'k5' => 'Rem Rusak',
    'k6' => 'Electric Starter Rusak',
    'k7' => 'Pompa Oli Rusak'
];

$rules = [
    'k1' => ['p1', 'p2', 'p3', 'p4', 'p5', 'p6'],
    'k2' => ['p1', 'p2', 'p7', 'p8', 'p9'],
    'k3' => ['p1', 'p10', 'p11', 'p12'],
    'k4' => ['p13', 'p14', 'p15', 'p16', 'p17'],
    'k5' => ['p18', 'p19', 'p20', 'p21'],
    'k6' => ['p22', 'p23', 'p24'],
    'k7' => ['p24', 'p25', 'p26', 'p27', 'p28']
];

// Forward Chaining Function
function forwardChaining($gejalaInput, $rules, $kerusakan)
{
    $hasil = [];

    foreach ($rules as $kerusakanId => $ruleGejala) {
        $intersect = array_intersect($ruleGejala, $gejalaInput);
        $confidence = count($intersect) / count($ruleGejala) * 100;

        if (!empty($intersect)) {
            $hasil[] = [
                'kerusakan' => $kerusakan[$kerusakanId],
                'confidence' => round($confidence, 2)
            ];
        }
    }

    // Fokus pada hasil dengan persentase terbesar
    usort($hasil, function ($a, $b) {
        return $b['confidence'] <=> $a['confidence'];
    });

    return $hasil;
}

// Jika Form Dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gejalaInput = isset($_POST['gejala']) ? $_POST['gejala'] : [];
    $hasil = forwardChaining($gejalaInput, $rules, $kerusakan);
} else {
    $gejalaInput = [];
    $hasil = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar - Forward Chaining</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center">Sistem Pakar Diagnosa Kerusakan</h1>
    <form method="POST" class="my-4">
        <h4>Pilih Gejala</h4>
        <div class="row">
            <?php foreach ($gejala as $key => $value): ?>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="gejala[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php echo in_array($key, $gejalaInput) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="<?php echo $key; ?>">
                            <?php echo $value; ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Diagnosa</button>
    </form>

    <?php if (!empty($hasil)): ?>
        <h4>Hasil Diagnosa</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kerusakan</th>
                    <th>Keyakinan (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hasil as $item): ?>
                    <tr>
                        <td><?php echo $item['kerusakan']; ?></td>
                        <td><?php echo $item['confidence']; ?>%</td>
                    </tr>
                    <?php break; ?> <!-- Fokus pada persentase terbesar -->
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
