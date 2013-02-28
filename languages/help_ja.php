<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_ja.php

//translator(s): 
$help["setup_mkdirMethod"] = "セーフモードをオンにするには、ファイル管理機能がフォルダを作成するために FTP アカウントを有効にしなければなりません。";
$help["setup_notifications"] = "利用者への電子メールによる通知(タスク割り当て、新規投稿、タスクの変更...)<br/>有効な SMTP/sendmail が必要です。";
$help["setup_forcedlogin"] = " &quot;false&quot;にセットすると、URL にログイン名/パスワードを含んだ外部からのリンクを遮断します。";
$help["setup_langdefault"] = "ログイン画面で初期値として表示される言語を選択してください。brank にしておくとブラウザの言語設定の検出を試みます。";
$help["setup_myprefix"] = "次に示すものと同じ名前のテーブルが既にデータベースにある場合は、このプレフィックスを指定してください。<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Leave blank for not use table prefix.";
$help["setup_loginmethod"] = "パスワードをデータベースに保管する方法です。<br/>CVS 認証とhtaccess認証を利用するときは、 &quot;Crypt&quot; にセットしてください(CVSサポート and/or htaccess 認証が有効になっているとき)。";
$help["admin_update"] = "バージョンをアップデートする際は、必ず次の順番で行ってください。<br/>1. 設定を編集 (新しい設定項目を補充してください)<br/>2. データベースを編集 (同意していただいた上で、先行バージョンからのアップデート)";
$help["task_scope_creep"] = "納期と完了日の差の日数です。 (正の数の場合は太字になります)";
$help["max_file_size"] = "アップロードできる 1 個のファイルの最大サイズです。";
$help["project_disk_space"] = "プロジェクトのファイルサイズの合計です。";
$help["project_scope_creep"] = "納期と完了日の差の日数です。(正の数の場合は太字になります) すべてのタスクの合計です。";
$help["mycompany_logo"] = "会社のロゴをアップロードしてください。サイトのタイトルに代わってヘッダに表示されます。";
$help["calendar_shortname"] = "月表示のカレンダーに表示されるラベルです。 必須入力です。";
$help["user_autologout"] = "クリックしないで何秒経過すると自動的にログアウトするかを設定します。無効にするには 0 を指定します。";
$help["user_timezone"] = "GMTとの時差を設定します。";
//2.4
$help["setup_clientsfilter"] = "接続している顧客だけを表示します。";
$help["setup_projectsfilter"] = "ユーザーがチームに所属しているプロジェクトだけを表示します。";
//2.5
$help["setup_notificationMethod"] = "通知の電子メールを発信する方法を指定します : 内蔵 php mail 関数 (PHPパラメータにSMTPサーバーかsendmailが設定されている必要があります) または、個人用のSMTPサーバー";
//2.5 fullo
$help["newsdesk_links"] = "複数のリンクを設定するには、セミコロンで区切ってください。";
?>
