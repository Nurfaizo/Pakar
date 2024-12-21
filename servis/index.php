<?php

$gejala = [
    'p1' => 'Mesin mendadak mati',
    'p2' => 'Terdapat pelumas pada kepala busi',
    'p3' => 'Warna busi menjadi cokelat/kemerahan',
    'p4' => 'Electrode meleleh',
    'p5' => 'Indikator engine berkedip',
    'p6' => 'Idle kasar saat RPM rendah',
    'p7' => 'Tenaga melemah saat akselerasi',
    'p8' => 'Akselerasi buruk',
    'p9' => 'Konsumsi BBM boros',
    'p10' => 'Suara mesin menggelitik',
    'p11' => 'Tenaga mesin loyo',
    'p12' => 'Terdengar bunyi gluduk saat lepas gas',
    'p13' => 'Tiba-tiba lost power',
    'p14' => 'Tidak ada air dalam radiator',
    'p15' => 'Oli bercampur air',
    'p16' => 'Bau terbakar di transmisi',
    'p17' => 'Bau menyengat pada mesin',
    'p18' => 'Tenaga mesin mendadak berkurang'
];

$kerusakan = [
    'r1' => 'Kerusakan Busi',
    'r2' => 'Kerusakan Injector',
    'r3' => 'Kerusakan Premature Ignition',
    'r4' => 'Mesin Overheat'
];

$rules = [
    'r1' => ['p1', 'p2', 'p3', 'p4', 'p5'],
    'r2' => ['p6', 'p7', 'p8', 'p9'],
    'r3' => ['p10', 'p11', 'p12', 'p13'],
    'r4' => ['p1', 'p14', 'p15', 'p16', 'p17', 'p18']
];

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
                    <?php break; ?> 
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
