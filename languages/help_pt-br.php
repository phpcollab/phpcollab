<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_en.php

//translator(s): Poorlyte <poorlyte@yahoo.com> (all versions)

$help["setup_mkdirMethod"] = "Se o modo de segurança está Ativo, você precisa definir uma conta de FTP para ser capaz de criar uma pasta para o gerenciamento do arquivo.";
$help["setup_notifications"] = "Notificações por email aos usuários:<br/><br/>&nbsp;&nbsp;- Atribuição de tarefa<br/>&nbsp;&nbsp;- Nova mensagem<br/>&nbsp;&nbsp;- Alterações nas tarefas<br/>&nbsp;&nbsp;- ect<br/><br/>Requerem um servidor de SMTP/SendMail válido.";
$help["setup_forcedlogin"] = "Se não, rejeita um link externo com o nome de usuário e senha na url.";
$help["setup_langdefault"] = "Escolha o idioma padrão selecionado na entrada do sistema ou deixe em branco para detecção automática do idioma através da configuração do navegador.";
$help["setup_myprefix"] = "Defina esta propriedade se você tiver tabelas com mesmo nome num mesmo banco de dados.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Deixem em branco caso não for usar um prefixo para as tabelas.";
$help["setup_loginmethod"] = "Método para armazenar as senhas no banco de dados.<br/><br/>Defina para &quot;Crypt&quot; para a autenticação do CVS e htaccess funcionarem caso estejam ativos.";
$help["admin_update"] = "Considere sempre a notificação indicando para você atualizar sua versão.<br/><br/>&nbsp;&nbsp;1. Editar Configurações - Opções gerais para FTP, servidor de email, temas, módulos suplementares, etc;<br/><br/>&nbsp;&nbsp;2. Editar Banco de Dados - Atualização dependendo da sua versão anterior do PhpCollab.";
$help["task_scope_creep"] = "Diferença, em dias, entre a data de término e a data de finalização (se houver a diferença o valor será mostrado em negrito).";
$help["max_file_size"] = "Tamanho máximo de um arquivo para upload.";
$help["project_disk_space"] = "Tamanho total dos arquivos relacionados ao projeto.";
$help["project_scope_creep"] = "Diferença, em dias, entre a data de encerração e a data de finalização (se houver diferença o valor será mostrado em negrito).<br/><br/>Tempo total para todas as tarefas do projeto.";
$help["mycompany_logo"] = "Envia o logotipo da sua empresa. A imagem irá aparecer no cabeçalho no lugar do título do site.";
$help["calendar_shortname"] = "Rótulo que aparecerá no calendário no modo de visualização mensal. Obrigatório.";
$help["user_autologout"] = "Tempo, em segundos, para ser desconectado após inatividade. Coloque 0 (zero) para desabilitar este recurso.";
$help["user_timezone"] = "Defina seu fuso horário (GMT).";
//2.4
$help["setup_clientsfilter"] = "Filtrar para ver somente os usuários de clientes conectados.";
$help["setup_projectsfilter"] = "Filtrar para ver somente o projeto quando o usuário estiver em uma equipe.";
//2.5
$help["setup_notificationMethod"] = "Define o método de envio de notificações por email:<br/><br/>&nbsp;&nbsp;a) Utilizando a função interna de email do PHP (para ter um servidor SMTP ou sendmail é necessário que os parâmetros necessários estejam configurados no PHP).<br/><br/>&nbsp;&nbsp;b) Utilizando um servidor SMTP personalizado.";
//2.5 fullo
$help["newsdesk_links"] = "Para adicionar múltiplos links relacionados use um ponto-e-vírgula entre cada link. Ex: http://php.org/; http://phpcollab.com/";
