<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_tr.php

//translator(s): 
$help["setup_mkdirMethod"] = "Eðer güvenli-mode aktif ise [safe-mode on] Dosya yönetimi ile klasör yaratabilmeniz için önce Ftp hesabý açmanýz gereklidir.";
$help["setup_notifications"] = "Kullanýcýlara e-posta bildirimleri için (görev atamasý, yeni mesaj, görev deðiþiklikleri...)<br/> Geçerli smtp/sendmail gereklidir.";
$help["setup_forcedlogin"] = "Eðer iptal edilmiþ ise, giriþ ekraný URL'sinde dýþ baðlantýlara izin vermez.";
$help["setup_langdefault"] = "Giriþ ekranýnda varsayýlan dil seçimi için seçin veya boþ býrakarak internet gezgininin dilinin otomatik algýlanmasýný kullanýn.";
$help["setup_myprefix"] = "Eðer mevcut veritabanýnda ayný isimde tablolar var ise bu deðeri kullanýn <br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Tablo adlarýna ön ek yapmamak için boþ býrakýn.";
$help["setup_loginmethod"] = "Veritabanýnda þifre saklama yöntemi.<br/>CVS doðrulamasý ve htaccess doðrulamasýnýn çalýþmasý için &quot;Crypt&quot; seçin (eðer CVS desteði ve/veya htaccess doðrulamalarý aktif edilmiþ ise).";
$help["admin_update"] = "Sürüm güncellemesi için belirtilen sýralamaya kesin olarak uyun<br/>1. Ayarlarý düzenleyin (yeni parametreleri doldurun)<br/>2. Veritabanýný düzenleyin (önceki sürümdeki anlaþmaya uygun olarak güncelleyin)";
$help["task_scope_creep"] = "Beklenen bitiþ tarihi ile tamamlanma tarihi arasýndaki gün farký (eðer sýfýrdan büyük ise koyu renklidir)";
$help["max_file_size"] = "Yüklenebilecek dosyanýn maksimum boyutu";
$help["project_disk_space"] = "Proje için toplam dosyalarýn büyüklüðü";
$help["project_scope_creep"] = "Beklenen bitiþ tarihi ile tamamlanma tarihi arasýndaki gün farký (eðer sýfýrdan büyük ise koyu renklidir). Bütün görevler için toplamdýr";
$help["mycompany_logo"] = "Firmanýzýn logosunu yükleyin . Site baþlýðý yerine baþta gözükür";
$help["calendar_shortname"] = "Aylýk takvim görüntüsünde gözükecek olan yazý. Mecburidir.";
$help["user_autologout"] = "Sistemde faaliyet olmamasý durumunda baðlantýnýn kesileceði saniye süresi. Ýptal etmek için 0 yazýn";
$help["user_timezone"] = "GMT zaman dilimini [timezone] ayarlayýn.";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
?>