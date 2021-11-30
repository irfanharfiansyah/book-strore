<?php

require_once("db.php");
require_once("component.php");



$con = Createdb();

// create button click
if (isset($_POST['create'])) {
    createData();
}

if (isset($_POST['update'])) {
    UpdateData();
}

if (isset($_POST['delete'])) {
    deleteRecord();
}

if (isset($_POST['deleteall'])) {
    deleteAll();
}

function createData()
{
    $bookname = textboxValue("book_name");
    $bookpublisher = textboxValue("book_publisher");
    $bookprice = textboxValue("book_price");
    $image_book = $_FILES['image_book']['name'];

    // CURL

    $nameFile = $_FILES['image_book']['name'];
    $typeFile = $_FILES['image_book']['type'];
    $tmpfile = $_FILES['image_book']['tmp_name'];

    $c_put_object = curl_init();
    $headers = array("opc-multipart:true");
    curl_setopt($c_put_object, CURLOPT_URL, 'https://objectstorage.ap-sydney-1.oraclecloud.com/p/Rxsl8nxT5Im88Ep8zaqj0AKuRvaNCJ6MpdtQ6Vfky8YWbi6reneq2L0Y8x64Mvl7/n/sdrtuckxr05z/b/bucket-UTS-Irfan/o/' . $nameFile);
    curl_setopt($c_put_object, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($c_put_object, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($c_put_object, CURLOPT_RETURNTRANSFER, TRUE);

    $output = curl_exec($c_put_object);

    $data = json_decode($output);

    $postfields = array(
        'uploaded_file' => file_get_contents($tmpfile)
    );

    $c_put_file = curl_init();
    curl_setopt($c_put_file, CURLOPT_URL, 'https://objectstorage.ap-sydney-1.oraclecloud.com/p/Rxsl8nxT5Im88Ep8zaqj0AKuRvaNCJ6MpdtQ6Vfky8YWbi6reneq2L0Y8x64Mvl7/n/sdrtuckxr05z/b/bucket-UTS-Irfan/u/' . $nameFile . '/id/' . $data->uploadId . "/1");
    curl_setopt($c_put_file, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($c_put_file, CURLOPT_POST, 1);
    curl_setopt($c_put_file, CURLOPT_POSTFIELDS, file_get_contents($tmpfile));
    curl_setopt($c_put_file, CURLOPT_HTTPHEADER, array('Content-Type: ' . $typeFile));
    curl_setopt($c_put_file, CURLOPT_RETURNTRANSFER, TRUE);

    $put_out = curl_exec($c_put_file);

    $c_post = curl_init();
    curl_setopt($c_post, CURLOPT_URL, 'https://objectstorage.ap-sydney-1.oraclecloud.com/p/Rxsl8nxT5Im88Ep8zaqj0AKuRvaNCJ6MpdtQ6Vfky8YWbi6reneq2L0Y8x64Mvl7/n/sdrtuckxr05z/b/bucket-UTS-Irfan/u/' . $nameFile . '/id/' . $data->uploadId . "/");
    curl_setopt($c_post, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($c_post, CURLOPT_POST, 1);

    $post_out = curl_exec($c_post);
    $imageBook = "https://objectstorage.ap-sydney-1.oraclecloud.com/p/Rxsl8nxT5Im88Ep8zaqj0AKuRvaNCJ6MpdtQ6Vfky8YWbi6reneq2L0Y8x64Mvl7/n/sdrtuckxr05z/b/bucket-UTS-Irfan/o/" . $nameFile;
// CURL

    if ($bookname && $bookpublisher && $bookprice && $image_book) {

        $sql = "INSERT INTO books (book_name, book_publisher, book_price, image_book) 
                        VALUES ('$bookname','$bookpublisher','$bookprice', '$image_book')";

        echo (mysqli_query($GLOBALS['con'], $sql));
        if (mysqli_query($GLOBALS['con'], $sql)) {
            TextNode("success", "Record Successfully Inserted...!");
        } else {
            echo "Error";
        }
    } else {
        TextNode("error", "Provide Data in the Textbox");
    }
}

function textboxValue($value)
{
    $textbox = mysqli_real_escape_string($GLOBALS['con'], trim($_POST[$value]));
    if (empty($textbox)) {
        return false;
    } else {
        return $textbox;
    }
}


// messages
function TextNode($classname, $msg)
{
    $element = "<h6 class='$classname'>$msg</h6>";
    echo $element;
}


// get data from mysql database
function getData()
{
    $sql = "SELECT * FROM books";

    $result = mysqli_query($GLOBALS['con'], $sql);

    if (mysqli_num_rows($result) > 0) {
        return $result;
    }
}

// update dat
function UpdateData()
{
    $bookid = textboxValue("book_id");
    $bookname = textboxValue("book_name");
    $bookpublisher = textboxValue("book_publisher");
    $bookprice = textboxValue("book_price");
    // $image_book= textboxValue("image_book");

    if ($bookname && $bookpublisher && $bookprice) {
        $sql = "
                    UPDATE books SET book_name='$bookname', book_publisher = '$bookpublisher', book_price = '$bookprice', image_book = '$image_book'  WHERE id='$bookid';                    
        ";

        if (mysqli_query($GLOBALS['con'], $sql)) {
            TextNode("success", "Data Successfully Updated");
        } else {
            TextNode("error", "Enable to Update Data");
        }
    } else {
        TextNode("error", "Select Data Using Edit Icon");
    }
}


function deleteRecord()
{
    $bookid = (int)textboxValue("book_id");

    $sql = "DELETE FROM books WHERE id=$bookid";

    if (mysqli_query($GLOBALS['con'], $sql)) {
        TextNode("success", "Record Deleted Successfully...!");
    } else {
        TextNode("error", "Enable to Delete Record...!");
    }
}


function deleteBtn()
{
    $result = getData();
    $i = 0;
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $i++;
            if ($i > 3) {
                buttonElement("btn-deleteall", "btn btn-danger", "<i class='fas fa-trash'></i> Delete All", "deleteall", "");
                return;
            }
        }
    }
}


function deleteAll()
{
    $sql = "DROP TABLE books";

    if (mysqli_query($GLOBALS['con'], $sql)) {
        TextNode("success", "All Record deleted Successfully...!");
        Createdb();
    } else {
        TextNode("error", "Something Went Wrong Record cannot deleted...!");
    }
}


// set id to textbox
function setID()
{
    $getid = getData();
    $id = 0;
    if ($getid) {
        while ($row = mysqli_fetch_assoc($getid)) {
            $id = $row['id'];
        }
    }
    return ($id + 1);
}
