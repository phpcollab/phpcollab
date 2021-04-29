<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/lang_pt.php

//translator(s): Carlos Figueiredo
$byteUnits = array('Bytes', 'KB', 'MB', 'GB');


$monthNameArray = array(
    1 => "Janeiro",
    "Fevereiro",
    "Mar&ccedil;o",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro"
);

$status = array(
    0 => "Cliente Completo",
    1 => "Completo",
    2 => "N&atilde;o come&ccedil;ado",
    3 => "Abrir",
    4 => "Suspendido"
);

$profil = array(
    0 => "Administrador",
    1 => "Gestor do Projecto",
    2 => "Utilizador",
    3 => "Cliente Utilizador",
    4 => "Disabled",
    5 => "Project Manager Administrator"
);

$priority = array(
    0 => "Nenhuma",
    1 => "Muito Baixa",
    2 => "Baixa",
    3 => "M&eacute;dia",
    4 => "Alta",
    5 => "Muito Alta"
);

$statusTopic = array(0 => "Fechado", 1 => "Aberto");
$statusTopicBis = array(0 => "Sim", 1 => "N&atilde;o");

$statusPublish = array(0 => "Sim", 1 => "N&atilde;o");

$statusFile = array(
    0 => "Aprovado",
    1 => "Aprovado com altera&ccedil;&otilde;es",
    2 => "Necessita Aprova&ccedil;&atilde;o",
    3 => "N&atilde;o necessita aprova&ccedil;&atilde;o",
    4 => "N&atilde;o Aprovado"
);



$strings["please_login"] = "Por favor registe-se";
$strings["requirements"] = "Requerimentos do Sistema";
$strings["login"] = "Registar";
$strings["no_items"] = "N&atilde;o h&aacute; itens para mostrar";
$strings["logout"] = "Sair";
$strings["preferences"] = "Prefer&ecirc;ncias";
$strings["my_tasks"] = "Minhas Tarefas";
$strings["edit_task"] = "Editar Tarefas";
$strings["copy_task"] = "Copiar Tarefas";
$strings["add_task"] = "Adicionar Tarefas";
$strings["delete_tasks"] = "Apagar Tarefas";
$strings["assignment_history"] = "Hist&oacute;rico de Delega&ccedil;&otilde;es";
$strings["assigned_on"] = "Delegado Em";
$strings["assigned_by"] = "Delegado Por";
$strings["to"] = "Para";
$strings["comment"] = "Comentar";
$strings["task_assigned"] = "Tarefa delegada a ";
$strings["task_unassigned"] = "Tarefa n&atilde;o delegada";
$strings["edit_multiple_tasks"] = "Editar V&aacute;rias Tarefas";
$strings["tasks_selected"] = "tarefas seleccionadas. Escolher novos valores para estas tarefas, ou seleccionar [Sem Altera&ccedil;&otilde;es] para reter valores actuais.";
$strings["assignment_comment"] = "Coment&aacute;rio da delega&ccedil;&atilde;o";
$strings["no_change"] = "[Sem Altera&ccedil;&otilde;es]";
$strings["my_discussions"] = "Minhas Discuss&otilde;es";
$strings["discussions"] = "Discuss&otilde;es";
$strings["delete_discussions"] = "Apagar Discuss&otilde;es";
$strings["delete_discussions_note"] = "Nota: Discuss&otilde;es n&atilde;o podem ser reabertas uma vez apagadas.";
$strings["topic"] = "T&oacute;pico";
$strings["posts"] = "correio";
$strings["latest_post"] = "&uacute;ltimo Correio";
$strings["my_reports"] = "Meus Relat&oacute;rios";
$strings["reports"] = "Relat&oacute;rios";
$strings["create_report"] = "Criar Relat&oacute;rio";
$strings["report_intro"] = "Seleccione os par&acirc;metros da sua tarefa aqui e guarde a pesquisa na p&aacute;gina de resultados depois de executar o seu relat&oacute;rio.";
$strings["admin_intro"] = "Configura&ccedil;&atilde;o e par&acirc;metros do Projecto.";
$strings["copy_of"] = "Copia de ";
$strings["add"] = "Adicionar";
$strings["delete"] = "Apagar";
$strings["remove"] = "Remover";
$strings["copy"] = "Copiar";
$strings["view"] = "Ver";
$strings["edit"] = "Editar";
$strings["update"] = "Actualizar";
$strings["details"] = "Detalhes";
$strings["none"] = "Nenhuma";
$strings["close"] = "Fechar";
$strings["new"] = "Nova";
$strings["select_all"] = "Seleccionar Todos";
$strings["unassigned"] = "N&atilde;o Delegada";
$strings["administrator"] = "Administrador";
$strings["my_projects"] = "Meus Projectos";
$strings["project"] = "Projecto";
$strings["active"] = "Activo";
$strings["inactive"] = "Inactivo";
$strings["project_id"] = "ID Projecto";
$strings["edit_project"] = "Editar Projecto";
$strings["copy_project"] = "Copiar Projecto";
$strings["add_project"] = "Adicionar Projecto";
$strings["clients"] = "Clientes";
$strings["organization"] = "Organiza&ccedil;&atilde;o do Cliente";
$strings["client_projects"] = "Projectos do Cliente";
$strings["client_users"] = "Utilizadores do Client";
$strings["edit_organization"] = "Editar Organiza&ccedil;&atilde;o do Cliente";
$strings["add_organization"] = "Adicionar Organiza&ccedil;&atilde;o do Cliente";
$strings["organizations"] = "Organiza&ccedil;&otilde;es do Cliente";
$strings["status"] = "Estado";
$strings["owner"] = "Dono";
$strings["home"] = "Início";
$strings["projects"] = "Projectos";
$strings["files"] = "Ficheiros";
$strings["search"] = "Procurar";
$strings["user"] = "Utilizador";
$strings["project_manager"] = "Gestor do Projecto";
$strings["due"] = "Finalizado";
$strings["task"] = "Tarefa";
$strings["tasks"] = "Tarefas";
$strings["team"] = "Equipa";
$strings["add_team"] = "Adicionar Membros de Equipa";
$strings["team_members"] = "Membros de Equipa";
$strings["full_name"] = "Nome Completo";
$strings["title"] = "Título";
$strings["user_name"] = "Nome Utilizador";
$strings["work_phone"] = "Telefone Trabalho";
$strings["priority"] = "Prioridade";
$strings["name"] = "Nome";
$strings["description"] = "Descri&ccedil;&atilde;o";
$strings["phone"] = "Telefone";
$strings["address"] = "Morada";
$strings["comments"] = "Coment&aacute;rios";
$strings["created"] = "Criado";
$strings["assigned"] = "Delegado";
$strings["modified"] = "Modificado";
$strings["assigned_to"] = "Delegado a";
$strings["due_date"] = "Data de Finaliza&ccedil;&atilde;o";
$strings["estimated_time"] = "Tempo Estimado";
$strings["actual_time"] = "Tempo Actual";
$strings["delete_following"] = "Apagar o seguinte?";
$strings["cancel"] = "Cancelar";
$strings["and"] = "e";
$strings["administration"] = "Administra&ccedil;&atilde;o";
$strings["user_management"] = "Gest&atilde;o Utilizador";
$strings["system_information"] = "Informa&ccedil;&atilde;o Sistema";
$strings["product_information"] = "Informa&ccedil;&atilde;o Produto";
$strings["system_properties"] = "Propriedades Sistema";
$strings["create"] = "Criar";
$strings["report_save"] = "Guarde este relat&oacute;rio query na sua homepage para o poder voltar a utilizar.";
$strings["report_name"] = "Nome Relat&oacute;rio";
$strings["save"] = "Guardar";
$strings["matches"] = "Iguais";
$strings["match"] = "Igual";
$strings["report_results"] = "Resultados Relat&oacute;rio";
$strings["success"] = "Sucesso";
$strings["addition_succeeded"] = "Adicionado com sucesso";
$strings["deletion_succeeded"] = "Apagado com sucesso";
$strings["report_created"] = "Criado relat&oacute;rio";
$strings["deleted_reports"] = "Relat&oacute;rios Apagados";
$strings["modification_succeeded"] = "Modifica&ccedil;&otilde;es feitas";
$strings["errors"] = "Erros encontrados!";
$strings["blank_user"] = "O utilizador n&atilde;o pôde ser encontrado.";
$strings["blank_organization"] = "O organiza&ccedil;&atilde;o do cliente n&atilde;o pôde ser localizada.";
$strings["blank_project"] = "O projecto n&atilde;o pôde ser localizado.";
$strings["user_profile"] = "Perfil Utilizador";
$strings["change_password"] = "Mudar Password";
$strings["change_password_user"] = "Mudar a password do utilizador.";
$strings["old_password_error"] = "A password antiga que introduziu est&aacute; incorrecta. Por favor reintroduza a password antiga.";
$strings["new_password_error"] = "As duas passwords que introduziu n&atilde;o s&atilde;o iguais. Por favor reintroduza a sua nova password.";
$strings["notifications"] = "Notifica&ccedil;&otilde;es";
$strings["change_password_intro"] = "Introduza a sua antiga password e depois introduza e confirme a sua nova password.";
$strings["old_password"] = "Antiga Password";
$strings["new_password"] = "Nova Password";
$strings["confirm_password"] = "Confirme Password";
$strings["home_phone"] = "Telefone Casa";
$strings["mobile_phone"] = "Telem&oacute;vel";
$strings["permissions"] = "Permiss&otilde;es";
$strings["administrator_permissions"] = "Permiss&otilde;es de Administrador";
$strings["project_manager_permissions"] = "Permiss&otilde;es Gestor de Projecto";
$strings["user_permissions"] = "Permiss&otilde;es de Utilizador";
$strings["account_created"] = "Conta Criada";
$strings["edit_user"] = "Editar Utilizador";
$strings["edit_user_details"] = "Editar os detalhes do utilizador.";
$strings["change_user_password"] = "Mudar a password do utilizador.";
$strings["select_permissions"] = "Seleccionar permiss&otilde;es para este utilizador";
$strings["add_user"] = "Adicionar Utilizador";
$strings["enter_user_details"] = "Introduzir detalhes para a conta do utilizador que est&aacute; a criar.";
$strings["enter_password"] = "Introduza a password do utilizador.";
$strings["success_logout"] = "Saiu com sucesso. Pode voltar a entrar introduzindo o nome de utilizador e a password em baixo.";
$strings["invalid_login"] = "O nome de utilizador e/ou password que introduziu s&atilde;o inv&aacute;lidos. Por favor reintroduza os seus dados de registo.";
$strings["profile"] = "Perfil";
$strings["user_details"] = "Detalhes da Conta de Utilizador.";
$strings["edit_user_account"] = "Edite a informa&ccedil;&atilde;o da sua conta.";
$strings["no_permissions"] = "N&atilde;o tem suficientes permiss&otilde;es para realizar esta opera&ccedil;&atilde;o.";
$strings["discussion"] = "Discuss&atilde;o";
$strings["retired"] = "Reformado";
$strings["last_post"] = "&uacute;ltimo Correio";
$strings["post_reply"] = "Resposta ao Correio";
$strings["posted_by"] = "Correio Enviado Por";
$strings["when"] = "Quando";
$strings["post_to_discussion"] = "Correio para Discussion";
$strings["message"] = "Mensagem";
$strings["delete_reports"] = "Apagar Relat&oacute;rios";
$strings["delete_projects"] = "apagar Projectos";
$strings["delete_organizations"] = "Apagar Organiza&ccedil;&otilde;es Cliente";
$strings["delete_organizations_note"] = "Nota: Isto vai apagar todos os utilizadores de clientes para estas organiza&ccedil;&otilde;es de clientes, e disassociar todos os projectos abertos para estas organiza&ccedil;&otilde;es clientes.";
$strings["delete_messages"] = "Apagar Mensagens";
$strings["attention"] = "Aten&ccedil;&atilde;o";
$strings["delete_teamownermix"] = "Removido com sucesso, mas o dono do projecto n&atilde;o pode ser removido da equipa do projecto.";
$strings["delete_teamowner"] = "Voc&ecirc; n&atilde;o pode remover o dono do projecto de equipa do project.";
$strings["enter_keywords"] = "Introduza as palavras-chave";
$strings["search_options"] = "Palavras-chave e Op&ccedil;&otilde;es de Busca";
$strings["search_note"] = "Voc&ecirc; tem de introduzir a informa&ccedil;&atilde;o no campo de Procura Por.";
$strings["search_results"] = "Resultados da Procura";
$strings["users"] = "Utilizadores";
$strings["search_for"] = "Procura Por";
$strings["results_for_keywords"] = "Resultados de Procura por Palavras-Chave";
$strings["add_discussion"] = "Adicionar Discuss&atilde;o";
$strings["delete_users"] = "Apagar Conta de utilizador";
$strings["reassignment_user"] = "Redelega&ccedil;&atilde;o de Projecto e Tarefas";
$strings["there"] = "Existem";
$strings["owned_by"] = "Pertencem aos utilizadores acima.";
$strings["reassign_to"] = "Antes de apagar utilizadores, redelegar estas a";
$strings["no_files"] = "Sem ficheiros ligados";
$strings["published"] = "Publicado";
$strings["project_site"] = "Site em Projecto";
$strings["approval_tracking"] = "Hist&oacute;rico de Aprova&ccedil;&otilde;es";
$strings["size"] = "Tamanho";
$strings["add_project_site"] = "Adicionar ao Site em Projecto";
$strings["remove_project_site"] = "Remover do Site em Projecto";
$strings["more_search"] = "Mais op&ccedil;&otilde;es de Procura";
$strings["results_with"] = "Procurar Resultados Com";
$strings["search_topics"] = "T&oacute;picos de Procura";
$strings["search_properties"] = "Propriedades de Procura";
$strings["date_restrictions"] = "Restri&ccedil;&otilde;es de Datas";
$strings["case_sensitive"] = "Sensível &agrave; letra";
$strings["yes"] = "Sim";
$strings["no"] = "N&atilde;o";
$strings["sort_by"] = "Organizar Por";
$strings["type"] = "Tipo";
$strings["date"] = "Data";
$strings["all_words"] = "todas as palavras";
$strings["any_words"] = "qualquer das palavras";
$strings["exact_match"] = "exactamente igual";
$strings["all_dates"] = "Todas as datas";
$strings["between_dates"] = "Entre as datas";
$strings["all_content"] = "Todo o conte&uacute;do";
$strings["all_properties"] = "Todas as propriedades";
$strings["no_results_search"] = "A procura n&atilde;o devolveu resultados.";
$strings["no_results_report"] = "O relat&oacute;rio n&atilde;o devolveu resultados.";
$strings["schema_date"] = "AAAA/MM/DD";
$strings["hours"] = "horas";
$strings["choice"] = "Escolha";
$strings["missing_file"] = "Ficheiro em falta!";
$strings["project_site_deleted"] = "O Site em Projecto foi apagado com sucesso.";
$strings["add_user_project_site"] = "O utilizador recebeu autoriza&ccedil;&atilde;o para aceder ao Site em Projecto.";
$strings["remove_user_project_site"] = "As permiss&otilde;es de utilizador foram removidas.";
$strings["add_project_site_success"] = "A coloca&ccedil;&atilde;o do Site em Projecto foi feita.";
$strings["remove_project_site_success"] = "A remo&ccedil;&atilde;o do Site em Projecto foi feita.";
$strings["add_file_success"] = "Ligado 1 item de conte&uacute;do.";
$strings["delete_file_success"] = "Ficheiro removido com sucesso.";
$strings["update_comment_file"] = "O coment&aacute;rio ao ficheiro foi actualizado com sucesso.";
$strings["session_false"] = "Erro na Sess&atilde;o";

$strings["forgot_pwd"] = "Forgot password ?";
$strings["blank_fields"] = "mandatory fiels";
$strings["setup_erase"] = "Erase the file setup.php!!";
$strings["no_file"] = "Nenhum arquivo selecionado";
$strings["scope_creep"] = "Escalada do escopo";
$strings["days"] = "Dias";
$strings["logo"] = "Logotipo";
$strings["remember_password"] = "Lembrar Senha";
$strings["client_add_task_note"] = "Nota: A tarefa inserida é registrada no banco de dados, mas aparece aqui apenas se for atribuída a um membro da equipe!";
$strings["noti_clientaddtask1"] = "Tarefa adicionada pelo cliente :";
$strings["noti_clientaddtask2"] = "Uma nova tarefa foi adicionada pelo cliente do site do projeto ao seguinte projeto :";
$strings["phase"] = "Fase";
$strings["phases"] = "Fases";
$strings["phase_id"] = "ID da fase";
$strings["current_phase"] = "Fase(s) ativa";
$strings["total_tasks"] = "Total de tarefas";
$strings["uncomplete_tasks"] = "Tarefas incompletas";
$strings["no_current_phase"] = "Nenhuma fase está ativa no momento";
$strings["true"] = "Verdade";
$strings["false"] = "Falso";
$strings["enable_phases"] = "Ativar fases";
$strings["phase_enabled"] = "Fase ativada";
$strings["order"] = "Ordem";
$strings["options"] = "Opções";
$strings["support"] = "Suporte";
$strings["support_request"] = "Solicitação de suporte";
$strings["support_requests"] = "Pedidos de Suporte";
$strings["support_id"] = "Identificação do Pedido";
$strings["my_support_request"] = "Minhas solicitações de suporte";
$strings["introduction"] = "Introdução";
$strings["submit"] = "Enviar";
$strings["support_management"] = "Gerenciamento de Suporte";
$strings["date_open"] = "Data de abertura";
$strings["date_close"] = "Data de Fechamento";
$strings["add_support_request"] = "Adicionar solicitação de suporte";
$strings["add_support_response"] = "Adicionar resposta de suporte";
$strings["respond"] = "Responder";
$strings["delete_support_request"] = "Solicitação de suporte excluída";
$strings["delete_request"] = "Excluir solicitação de suporte";
$strings["delete_support_post"] = "Excluir postagem de suporte";
$strings["new_requests"] = "Novos pedidos";
$strings["open_requests"] = "Pedidos abertos";
$strings["closed_requests"] = "Solicitações completas";
$strings["manage_new_requests"] = "Gerenciar novas solicitações";
$strings["manage_open_requests"] = "Gerenciar solicitações abertas";
$strings["manage_closed_requests"] = "Gerenciar solicitações completas";
//2.0
$strings["add_subtask"] = "Adicionar subtarefa";
//2.1
$strings["bookmark"] = "marca páginas";
$strings["bookmarks"] = "marca páginas";
$strings["bookmark_category"] = "Categoria";
$strings["bookmark_category_new"] = "Nova categoria";
$strings["bookmarks_all"] = "Tudo";
$strings["bookmarks_my"] = "Meus marcadores de livro";
$strings["my"] = "Meus";
$strings["bookmarks_private"] = "Privativo";
$strings["shared"] = "Compartilhado";
$strings["private"] = "Privativo";
$strings["add_bookmark"] = "Adicionar marcador";
$strings["edit_bookmark"] = "Editar marcador";
$strings["delete_bookmarks"] = "Excluir favoritos";
$strings["team_subtask_details"] = "Detalhes da subtarefa da equipe";
$strings["client_subtask_details"] = "Detalhes da subtarefa do cliente";
$strings["client_change_status_subtask"] = "Altere seu status abaixo quando você concluir esta subtarefa";
$strings["disabled_permissions"] = "Conta desativada";
//2.4
