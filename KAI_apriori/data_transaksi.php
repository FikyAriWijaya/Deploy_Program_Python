<?php
//session_start();
if (!isset($_SESSION['apriori_toko_id'])) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "import/excel_reader2.php";
?>
<!DOCTYPE html>
    <head>
        <link rel="stylesheet" href="css/font-awesome.css">
        <link rel="stylesheet" href="css/animate.css">
        <link rel="stylesheet" href="css/templatemo-misc.css">
        <link rel="stylesheet" href="css/templatemo-style.css">
        <link rel="stylesheet" href="scripts/ionicons/css/ionicons.min.css">
    </head>
<section class="page_head">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="page_title">
                <h2 style="font-weight: bold; margin-left: -14px; margin-top: 25px; margin-bottom: 15px; color: #ff8c00; font-size: 30px; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.10);">Masukkan Dataset</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
//object database class
$db_object = new database();

$pesan_error = $pesan_success = "";
if(isset($_GET['pesan_error'])){
    $pesan_error = $_GET['pesan_error'];
}
if(isset($_GET['pesan_success'])){
    $pesan_success = $_GET['pesan_success'];
}

if(isset($_POST['submit'])){
    // if(!$input_error){
    $data = new Spreadsheet_Excel_Reader($_FILES['file_data_transaksi']['tmp_name']);

        $baris = $data->rowcount($sheet_index=0);
        $column = $data->colcount($sheet_index=0);
        //import data excel dari baris kedua, karena baris pertama adalah nama kolom
        // $temp_date = $temp_barang = "";
        for ($i=2; $i<=$baris; $i++) {
            for($c=1; $c<=$column; $c++){
                $value[$c] = $data->val($i, $c);
            }
                    $table = "transaksi";
                    $invoice = $value[1];
                    $temp_date = format_date($value[2]);
                    $barangIn = $value[3];
                    
                    //mencegah ada jarak spasi
                    $barangIn = str_replace(" ,", ",", $barangIn);
                    $barangIn = str_replace("  ,", ",", $barangIn);
                    $barangIn = str_replace("   ,", ",", $barangIn);
                    $barangIn = str_replace("    ,", ",", $barangIn);
                    $barangIn = str_replace(", ", ",", $barangIn);
                    $barangIn = str_replace(",  ", ",", $barangIn);
                    $barangIn = str_replace(",   ", ",", $barangIn);
                    $barangIn = str_replace(",    ", ",", $barangIn);

                    $sql = "INSERT INTO transaksi (invoice, transaction_date, barang) VALUES ";
                    $value_in = array();
                    $sql .= " ('$invoice', '$temp_date', '$barangIn')";
                    $db_object->db_query($sql);
        }
        ?>
        <script> location.replace("?menu=data_transaksi&pesan_success=Data berhasil disimpan"); </script>
        <?php
}

if(isset($_POST['delete'])){
    $sql = "TRUNCATE transaksi";
    $db_object->db_query($sql);
    ?>
        <script> location.replace("?menu=data_transaksi&pesan_success=Data transaksi berhasil dihapus"); </script>
        <?php
}

$sql = "SELECT
        *
        FROM
         transaksi";
$query=$db_object->db_query($sql);
$jumlah=$db_object->db_num_rows($query);
?>

<div class="super_sub_content">
    <div class="container">
        <div class="row">
            <!--UPLOAD EXCEL FORM-->
            <form method="post" enctype="multipart/form-data" action="">
                <div class="form-group">
                    <div class="input-group">
                        <label>Import data dari excel sesuai <a href="DATABASE/Catatan.txt">FORMAT</a></label>
                        <input name="file_data_transaksi" type="file" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <input name="submit" type="submit" value="Import Data" class="btn btn-success">
                </div>
                <div class="form-group">
                    <button name="delete" type="submit"  class="btn btn-danger" >
                        Hapus Semua
                    </button>
                </div>
            </form>

            <?php
            if (!empty($pesan_error)) {
                display_error($pesan_error);
            }
            if (!empty($pesan_success)) {
                display_success($pesan_success);
            }


            echo "Jumlah data: ".$jumlah."<br>";
            if($jumlah==0){
                    echo "Data kosong...";
            }
            else{
            ?>
            <table class='table table-bordered table-striped  table-hover'>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Barang</th>
            </tr>
                <?php
                    $no=1;
                    while($row=$db_object->db_fetch_array($query)){
                        echo "<tr>";
                            echo "<td>".$no."</td>";
                            echo "<td>".$row['invoice']."</td>";
                            echo "<td>".$row['transaction_date']."</td>";
                            echo "<td>".$row['barang']."</td>";
                        echo "</tr>";
                        $no++;
                    }
                    ?>
            </table>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
function get_barang_to_in($barang){
    $ex = explode(",", $barang);
    for ($i=0; $i < count($ex); $i++) { 

        $jml_key = array_keys($ex, $ex[$i]);
        if(count($jml_key)>1){
            unset($ex[$i]);
        }

    }
    return implode(",", $ex);
}

?>