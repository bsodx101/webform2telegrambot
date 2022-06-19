<?php

$type = $_POST['user_type'];
$district= $_POST['user_district'];
$street = $_POST['user_street'];
$housing_coop = $_POST['user_housing_coop'];
$level = $_POST['user_level'];
$land_plot = $_POST['user_land_plot'];
$condition = $_POST['user_condition'];
$square = $_POST['user_square'];
$badrooms = $_POST['user_badrooms'];
$view = $_POST['user_view'];
$description = $_POST['user_description'];
$name = $_POST['user_name'];
$phone = $_POST['user_phone1'];
$phone2 = $_POST['user_phone2'];
$telegramacc = $_POST['user_telegramacc'];
$price = $_POST['user_price'];
$tags = $_POST['user_tags'];
$tips = $_POST['user_tips'];


/* Подключаем telegram API*/

$token = "API key";
$chat_id = "chat id";

$arr = [


  'Тип:' => $type,
  'Район:' => $district,
  'Улица:' => $street,
  'ЖК:' => $housing_coop,
  'Этаж:' => $level,
  'Земельный участок:' => $land_plot,
  'Состояние:' => $condition,
  'Площадь:' => $square,
  'Количество спален:' => $badrooms,
  'Вид:' => $view,
  'Описание:' => $description,
  'Риелтор:' => $name,
  'Телефон:' => $phone,
  'Доп.телефон:' => $phone2,
  'Telegram:' => $telegramacc,
  'Цена💰:' => $price,
  'Комиссия:' => $tips,
  'Теги:' => $tags

];

$files = null;

if($_FILES['files']['tmp_name'])
{

    $txt = '';
    foreach($arr as $key => $value)
    {
        if($value == false)
        {
            continue;
        }
        
        $txt .= $key."  ".$value."\n";
    }

    $result = sendPhotos($token, $chat_id, $txt);
}
else
{
    $txt = '';
    foreach($arr as $key => $value)
    {
        if($value == false)
        {
            continue;
        }

        $txt .= "<b>".$key."</b> ".$value."\n";
    }

    $result = sendToTelegram($token, $chat_id, $txt);
}


if($result)
{
    header('Location: thank-you.html');
}
else
{
    echo 'Чтобы отправить объявление - необходимо добавить фото';
    die;
}

function sendToTelegram($token, $chat_id, $txt)
{
    $website = "https://api.telegram.org/bot".$token;
    $params = [
        'chat_id' => $chat_id,
        'parse_mode' => 'html',
        'text' => $txt,
    ];
    $ch = curl_init($website . '/sendMessage');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return (json_decode($result, 1) ? json_decode($result, 1) : $result);
}

function sendPhotos($token, $chat_id, $txt)
{

    $website = "https://api.telegram.org/bot".$token;

    $ch = curl_init($website . '/sendMediaGroup');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   

    foreach($_FILES['files']['tmp_name'] as $key => $val)
    {
        
        $media[$key] = ['type' => 'photo', 'media' => 'attach://'. $_FILES['files']['name'][$key] ];

        if($key == 0)
        {
            $media[$key]['caption'] = $txt;
        }

        $arFiles[$_FILES['files']['name'][$key]] = new \CURLFile(realpath($val));
    }

    $postContent = [
        'chat_id' => $chat_id,
        'media' => json_encode($media),
        'text' => $txt,
        'parse_mode' => 'HTML'
    ];

    $postContent = array_merge($postContent, $arFiles);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($postContent));
    $result = curl_exec($ch);
    curl_close($ch);
    return (json_decode($result, 1) ? json_decode($result, 1) : $result);
}

?>