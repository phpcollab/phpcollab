<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/lang_en.php

//translator(s): Poorlyte <poorlyte@yahoo.com> (<2.0;2.0;2.2;2.3;2.4;2.5)
//translator(s): Felipe Fonseca <http://hipercortex.tk> (<2.0)

$byteUnits = array('Bytes', 'KB', 'MB', 'GB');
$dayNameArray = array(
    1 => "Segunda-Feira",
    2 => "Terça-Feira",
    3 => "Quarta-Feira",
    4 => "Quinta-Feira",
    5 => "Sexta-Feira",
    6 => "Sábado",
    7 => "Domingo"
);
$monthNameArray = array(
    1 => "Janeiro",
    "Fevereiro",
    "Março",
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
$status = array(0 => "Finalizado pelo Cliente", 1 => "Finalizado", 2 => "Não Iniciado", 3 => "Aberto", 4 => "Suspenso");
$profil = array(
    0 => "Administrador",
    1 => "Gerente de Projeto",
    2 => "Usuário",
    3 => "Usuário de Cliente",
    4 => "Inativo",
    5 => "Diretor de Projeto"
);
$priority = array(0 => "Nenhuma", 1 => "Muito Baixa", 2 => "Baixa", 3 => "Média", 4 => "Alta", 5 => "Máxima");
$statusTopic = array(0 => "Encerrado", 1 => "Aberto");
$statusTopicBis = array(0 => "Sim", 1 => "Não");
$statusPublish = array(0 => "Sim", 1 => "Não");
$statusFile = array(
    0 => "Aprovado",
    1 => "Aprovado com Alterações",
    2 => "Precisa de Aprovação",
    3 => "Não Precisa de Aprovação",
    4 => "Não Aprovado"
);
$phaseStatus = array(0 => "Não Iniciada", 1 => "Aberta", 2 => "Completa", 3 => "Suspensa");
$requestStatus = array(0 => "Novo", 1 => "Aberto", 2 => "Completo");
$invoiceStatus = array(0 => "Aberta", 1 => "Enviada", 2 => "Paga");

$strings["please_login"] = "Identifique-se";
$strings["requirements"] = "Requisitos do Sistema";
$strings["login"] = "Entrar";
$strings["no_items"] = "Não há itens para exibição";
$strings["logout"] = "Sair";
$strings["preferences"] = "Preferências";
$strings["my_tasks"] = "Minhas Tarefas";
$strings["edit_task"] = "Editar Tarefa";
$strings["copy_task"] = "Copiar Tarefa";
$strings["add_task"] = "Adicionar Tarefa";
$strings["delete_tasks"] = "Excluir Tarefa";
$strings["assignment_history"] = "Histórico de Atribuições";
$strings["assigned_on"] = "Atribuído Em";
$strings["assigned_by"] = "Atribuído Por";
$strings["to"] = "Para";
$strings["comment"] = "Comentário";
$strings["task_assigned"] = "Tarefa atribuída a ";
$strings["task_unassigned"] = "Tarefa não atribuída";
$strings["edit_multiple_tasks"] = "Editar Múltiplas Tarefas";
$strings["tasks_selected"] = "tarefas selecionadas. Escolha novos valores para estas tarefas, ou selecione [Sem Alterações] para manter os valores atuais.";
$strings["assignment_comment"] = "Comentário da Atribuição";
$strings["no_change"] = "[Sem Alterações]";
$strings["my_discussions"] = "Meus Debates";
$strings["discussions"] = "Debates";
$strings["delete_discussions"] = "Excluir Debates";
$strings["delete_discussions_note"] = "Nota: Debates não podem ser reabertos uma vez que foram excluídos.";
$strings["topic"] = "Tópico";
$strings["posts"] = "Mensagens";
$strings["latest_post"] = "Última Mensagem";
$strings["my_reports"] = "Meus Relatórios";
$strings["reports"] = "Relatórios";
$strings["create_report"] = "Criar Relatório";
$strings["report_intro"] = "Selecione os critérios do relatório aqui e salve-os na página de resultados após executar seu relatório.";
$strings["admin_intro"] = "Opções e Configurações do Sistema.";
$strings["copy_of"] = "Cópia de ";
$strings["add"] = "Adicionar";
$strings["delete"] = "Excluir";
$strings["remove"] = "Remover";
$strings["copy"] = "Copiar";
$strings["view"] = "Exibir";
$strings["edit"] = "Editar";
$strings["update"] = "Atualizar";
$strings["details"] = "Detalhes";
$strings["none"] = "Não";
$strings["close"] = "Encerrar";
$strings["new"] = "Novo";
$strings["select_all"] = "Selecionar Tudo";
$strings["unassigned"] = "Não Atribuído";
$strings["administrator"] = "Administrador";
$strings["my_projects"] = "Meus Projetos";
$strings["project"] = "Projeto";
$strings["active"] = "Ativo";
$strings["inactive"] = "Inativo";
$strings["project_id"] = "ID do Projeto";
$strings["edit_project"] = "Editar Projeto";
$strings["copy_project"] = "Copiar Projeto";
$strings["add_project"] = "Adicionar Projeto";
$strings["clients"] = "Clientes";
$strings["organization"] = "Organização do Cliente";
$strings["client_projects"] = "Projetos do Cliente";
$strings["client_users"] = "Usuários do Cliente";
$strings["edit_organization"] = "Editar a Organização do Cliente";
$strings["add_organization"] = "Adicionar a Organização do Cliente";
$strings["organizations"] = "Clientes";
$strings["info"] = "Informação";
$strings["status"] = "Situação";
$strings["owner"] = "Dono";
$strings["home"] = "Início";
$strings["projects"] = "Projetos";
$strings["files"] = "Arquivos";
$strings["search"] = "Procurar";
$strings["admin"] = "Administração";
$strings["user"] = "Usuário";
$strings["project_manager"] = "Gerente de Projeto";
$strings["due"] = "Encerrado";
$strings["task"] = "Tarefa";
$strings["tasks"] = "Tarefas";
$strings["team"] = "Equipe";
$strings["add_team"] = "Adicionar Membros à Equipe";
$strings["team_members"] = "Membros da Equipe";
$strings["full_name"] = "Nome Completo";
$strings["title"] = "Título";
$strings["user_name"] = "Nome de Usuário";
$strings["work_phone"] = "Telefone Comercial";
$strings["priority"] = "Prioridade";
$strings["name"] = "Nome";
$strings["id"] = "ID";
$strings["description"] = "Descrição";
$strings["phone"] = "Telefone";
$strings["url"] = "URL";
$strings["address"] = "Endereço";
$strings["comments"] = "Comentários";
$strings["created"] = "Criado";
$strings["assigned"] = "Atribuído";
$strings["modified"] = "Modificado";
$strings["assigned_to"] = "Atribuído à";
$strings["due_date"] = "Data de Término";
$strings["estimated_time"] = "Tempo Estimado";
$strings["actual_time"] = "Tempo Real";
$strings["delete_following"] = "Excluír os seguintes?";
$strings["cancel"] = "Cancelar";
$strings["and"] = "e";
$strings["administration"] = "Administração";
$strings["user_management"] = "Gerenciamento de Usuários";
$strings["system_information"] = "Informações do Sistema";
$strings["product_information"] = "Informações do Produto";
$strings["system_properties"] = "Propriedades do Sistema";
$strings["create"] = "Criar";
$strings["report_save"] = "Salvar este relatório em sua página inicial para poder executá-lo novamente.";
$strings["report_name"] = "Nome do Relatório";
$strings["save"] = "Salvar";
$strings["matches"] = "Resultados";
$strings["match"] = "Resultado";
$strings["report_results"] = "Resultados do Relatório";
$strings["success"] = "Sucesso";
$strings["addition_succeeded"] = "Adição efetuada com sucesso. ";
$strings["deletion_succeeded"] = "Exclusão efetuada com sucesso. ";
$strings["report_created"] = "Relatório criado";
$strings["deleted_reports"] = "Relatórios excluídos";
$strings["modification_succeeded"] = "Alteração efetuada com sucesso. ";
$strings["errors"] = "Erros encontrados!";
$strings["blank_user"] = "Usuário não pôde ser localizado.";
$strings["blank_organization"] = "A organização do cliente não pôde ser localizada.";
$strings["blank_project"] = "O projeto não pôde ser localizado.";
$strings["user_profile"] = "Perfil do Usuário";
$strings["change_password"] = "Alterar Senha";
$strings["change_password_user"] = "Alterar a senha do usuário.";
$strings["old_password_error"] = "A senha antiga digitada está incorreta. Por favor digite novamente a senha antiga.";
$strings["new_password_error"] = "As duas senhas digitadas não são iguais. Por favor digite novamente a sua nova senha.";
$strings["notifications"] = "Notificações";
$strings["change_password_intro"] = "Digite sua senha antiga e em seguida digite e confirme sua nova senha.";
$strings["old_password"] = "Senha Antiga";
$strings["password"] = "Senha";
$strings["new_password"] = "Nova Senha";
$strings["confirm_password"] = "Confirme a Nova Senha";
$strings["email"] = "E-Mail";
$strings["home_phone"] = "Telefone Residencial";
$strings["mobile_phone"] = "Telefone Celular";
$strings["fax"] = "Fax";
$strings["permissions"] = "Permissões";
$strings["administrator_permissions"] = "Administrador";
$strings["project_manager_permissions"] = "Gerente de Projeto";
$strings["user_permissions"] = "Usuário";
$strings["account_created"] = "Conta Criada";
$strings["edit_user"] = "Editar Usuário";
$strings["edit_user_details"] = "Editar detalhes do usuário.";
$strings["change_user_password"] = "Alterar a senha do usuário.";
$strings["select_permissions"] = "Selecionar permissões para este usuário";
$strings["add_user"] = "Adicionar Usuário";
$strings["enter_user_details"] = "Informe os detalhes para esta conta de usuário que está sendo criada.";
$strings["enter_password"] = "Digite a senha do usuário.";
$strings["success_logout"] = "Você saiu com sucesso. Você pode entrar novamente digitando seu nome de usuário e senha abaixo.";
$strings["invalid_login"] = "O nome de usuário e/ou a senha digita são inválidos. Por favor digite suas informações corretamente para entrar no sistema.";
$strings["profile"] = "Perfil";
$strings["user_details"] = "Detalhes do usuário.";
$strings["edit_user_account"] = "Editar minhas informações.";
$strings["no_permissions"] = "Você não possui permissões suficientes para realizar esta operação.";
$strings["discussion"] = "Debate";
$strings["retired"] = "Encerrado";
$strings["last_post"] = "Última Mensagem";
$strings["post_reply"] = "Responder";
$strings["posted_by"] = "Enviado Por";
$strings["when"] = "Quando";
$strings["post_to_discussion"] = "Enviar ao Debate";
$strings["message"] = "Mensagem";
$strings["delete_reports"] = "Excluir Relatórios";
$strings["delete_projects"] = "Excluir Projetos";
$strings["delete_organizations"] = "Excluir Organizações do Cliente";
$strings["delete_organizations_note"] = "Nota: Isto irá excluir todos os usuários para esta organização, e desfará a associação a todos os projetos ativos desta organização.";
$strings["delete_messages"] = "Excluir Mensagens";
$strings["attention"] = "Atenção";
$strings["delete_teamownermix"] = "Excluído com sucesso, mas o dono do projeto não pode ser removido da equipe.";
$strings["delete_teamowner"] = "Você não pode remover o dono do projeto da equipe.";
$strings["enter_keywords"] = "Informe as palavras-chave";
$strings["search_options"] = "Opções de Procura";
$strings["search_note"] = "Você precisa entrar com as palavras-chave de procura.";
$strings["search_results"] = "Resultados da Procura";
$strings["users"] = "Usuários";
$strings["search_for"] = "Procurar por";
$strings["results_for_keywords"] = "Resultados da procura pelas palavras-chave";
$strings["add_discussion"] = "Adicionar Debate";
$strings["delete_users"] = "Excluir Usuário(s)";
$strings["reassignment_user"] = "Atribuição de Projetos e Tarefas";
$strings["there"] = "Há";
$strings["owned_by"] = "pertencentes aos usuários acima.";
$strings["reassign_to"] = "Antes de excluir os usuários, atribua os itens acima a";
$strings["no_files"] = "Não há arquivos relacionados";
$strings["published"] = "Publicado";
$strings["project_site"] = "Site do Projeto";
$strings["approval_tracking"] = "Acompanhamento de Aprovação";
$strings["size"] = "Tamanho";
$strings["add_project_site"] = "Incluir ao Site do Projeto";
$strings["remove_project_site"] = "Remover do Site do Projeto";
$strings["more_search"] = "Mais opções de procura";
$strings["results_with"] = "Encontrar Resultados Com";
$strings["search_topics"] = "Tópicos de Procura";
$strings["search_properties"] = "Propriedades de Procura";
$strings["date_restrictions"] = "Restrições de Datas";
$strings["case_sensitive"] = "Diferenciar Letras Maiúsculas de Minísculas";
$strings["yes"] = "Sim";
$strings["no"] = "Não";
$strings["sort_by"] = "Ordenar por";
$strings["type"] = "Tipo";
$strings["date"] = "Data";
$strings["all_words"] = "todas as palavras";
$strings["any_words"] = "qualquer uma das palavras";
$strings["exact_match"] = "exatamente igual";
$strings["all_dates"] = "Todas as datas";
$strings["between_dates"] = "Entre as datas";
$strings["all_content"] = "Todo Conteúdo";
$strings["all_properties"] = "Todas as propriedades";
$strings["no_results_search"] = "Nenhum resultado foi encontrado na procura.";
$strings["no_results_report"] = "Nenhum resultado foi encontrado para o relatório.";
$strings["schema_date"] = "DD/MM/YYYY";
$strings["hours"] = "horas";
$strings["choice"] = "Escolha";
$strings["missing_file"] = "Arquivo ausente!";
$strings["project_site_deleted"] = "O site do projeto foi excluído com sucesso.";
$strings["add_user_project_site"] = "Foi concedida permissão de acesso ao site do projeto ao usuário.";
$strings["remove_user_project_site"] = "As permissões do usuário foram removidas.";
$strings["add_project_site_success"] = "A adição ao site do projeto foi efetuada com sucesso.";
$strings["remove_project_site_success"] = "A remoção do site do projeto foi efetuada com sucesso.";
$strings["add_file_success"] = "1 arquivo foi relacionado.";
$strings["delete_file_success"] = "Remoção efetuada com sucesso.";
$strings["update_comment_file"] = "A atualização do comentário foi efetuada com sucesso.";
$strings["session_false"] = "Erro de Sessão";
$strings["logs"] = "Atividades";
$strings["logout_time"] = "Sair Automáticamente";
$strings["noti_foot1"] = "Esta notificação foi gerada pelo PHPCollab.";
$strings["noti_foot2"] = "Para ver sua página inicial, visite:";
$strings["noti_taskassignment1"] = "Nova Tarefa:";
$strings["noti_taskassignment2"] = "Uma nova tarefa foi atribuída a você:";
$strings["noti_moreinfo"] = "Para maiores informações, visite:";
$strings["noti_prioritytaskchange1"] = "Prioridade da Tarefa alterada:";
$strings["noti_prioritytaskchange2"] = "A prioridade da seguinte tarefa foi alterada:";
$strings["noti_statustaskchange1"] = "Status da Tarefa alterado:";
$strings["noti_statustaskchange2"] = "O status da seguinte tarefa foi alterado:";
$strings["login_username"] = "Você precisa digitar um nome de usuário.";
$strings["login_password"] = "Por favor digite a senha.";
$strings["login_clientuser"] = "Este é uma conta de usuário de cliente. Você não pode acessar o PhpCollab com uma conta de usuário de cliente.";
$strings["user_already_exists"] = "Já existe um usuário com este nome. Por favor escolha um novo nome ou uma variação deste nome de usuário.";
$strings["noti_duedatetaskchange1"] = "Data de encerramento da Tarefa alterada:";
$strings["noti_duedatetaskchange2"] = "A data de encerramento da seguinte tarefa foi alterada:";
$strings["company"] = "Empresa";
$strings["show_all"] = "Exibir Tudo";
$strings["information"] = "Informação";
$strings["delete_message"] = "Excluir esta mensagem";
$strings["project_team"] = "Equipe do Projeto";
$strings["document_list"] = "Lista de Documentos";
$strings["bulletin_board"] = "Quadro de Avisos";
$strings["bulletin_board_topic"] = "Tópico do Quadro de Avisos";
$strings["create_topic"] = "Criar um Novo Tópico";
$strings["topic_form"] = "Formulário Topic Form";
$strings["enter_message"] = "Digite sua mensagem";
$strings["upload_file"] = "Enviar um Arquivo";
$strings["upload_form"] = "Enviar Formulário";
$strings["upload"] = "Enviar";
$strings["document"] = "Documento";
$strings["approval_comments"] = "Comentários da Aprovação";
$strings["client_tasks"] = "Tarefas do Cliente";
$strings["team_tasks"] = "Tarefas da Equipe";
$strings["team_member_details"] = "Detalhes do Membro da Equipe";
$strings["client_task_details"] = "Detalhes da Tarefa do Cliente";
$strings["team_task_details"] = "Detalhes da Tarefa da Equipe";
$strings["language"] = "Idioma";
$strings["welcome"] = "Bem-vindo";
$strings["your_projectsite"] = "ao Site do Projeto";
$strings["contact_projectsite"] = "Se você tem alguma dúvida sobre a extranet ou informações encontradas aqui, entre em contado com o líder do projeto";
$strings["company_details"] = "Detalhes da Empresa";
$strings["database"] = "Backup e Restauração do Banco de Dados";
$strings["company_info"] = "Editar informações da empresa";
$strings["create_projectsite"] = "Criar Site Para o Projeto";
$strings["projectsite_url"] = "URL para o Site do Projeto";
$strings["design_template"] = "Projetar Modelo";
$strings["preview_design_template"] = "Pré-Visualizar o Projeto do Modelo";
$strings["delete_projectsite"] = "Excluir o Site do Projeto";
$strings["add_file"] = "Adicionar Arquivo";
$strings["linked_content"] = "Conteúdo Relacionado";
$strings["edit_file"] = "Editar detalhes do arquivo";
$strings["permitted_client"] = "Permitido a Usuários do Cliente";
$strings["grant_client"] = "Permitir Visualizar o Site do Projeto";
$strings["add_client_user"] = "Adicionar Usuário do Cliente";
$strings["edit_client_user"] = "Editar Usuário do Cliente";
$strings["client_user"] = "Usuário do Cliente";
$strings["client_change_status"] = "Altere seu status abaixo quando encerrar esta tarefa.";
$strings["project_status"] = "Status do Projeto";
$strings["view_projectsite"] = "Visualizar o Site do Projeto";
$strings["enter_login"] = "Digite seu nome de usuário para receber uma nova senha";
$strings["send"] = "Enviar";
$strings["no_login"] = "Nome de usário não cadastrado";
$strings["email_pwd"] = "Nova senha enviada";
$strings["no_email"] = "Usuário não possui um email";
$strings["forgot_pwd"] = "Esqueceu a senha?";
$strings["project_owner"] = "Você só pode fazer alterações em seus próprios projetos.";
$strings["connected"] = "Conectado";
$strings["session"] = "Sessão";
$strings["last_visit"] = "Última visita";
$strings["compteur"] = "Countador";
$strings["ip"] = "IP";
$strings["task_owner"] = "Você não faz parte da equipe deste projeto";
$strings["export"] = "Exportar";
$strings["reassignment_clientuser"] = "Reatribuição de Tarefa";
$strings["organization_already_exists"] = "Este nome já está em uso no sistema. Por favor escolha outro.";
$strings["blank_organization_field"] = "Você precisa digitar o nome da organização do cliente.";
$strings["blank_fields"] = "campos obrigatórios";
$strings["projectsite_login_fails"] = "Não foi possível confirmar a combinação de nome de usuário e senha.";
$strings["start_date"] = "Data de Início";
$strings["completion"] = "Andamento";
$strings["update_available"] = "Atualização disponível!";
$strings["version_current"] = "Você está usando a versão";
$strings["version_latest"] = "Versão mais recente é";
$strings["sourceforge_link"] = "Ver o site do projeto no Sourceforge";
$strings["demo_mode"] = "Modo de Demontração. Ação não disponível.";
$strings["setup_erase"] = "Apague o arquivo setup.php!!";
$strings["no_file"] = "Nenhum arquivo selecionado";
$strings["exceed_size"] = "O arquivo excedeu o tamanho máximo disponível";
$strings["no_php"] = "Arquivos PHP não são permitidos";
$strings["approval_date"] = "Data de Aprovação";
$strings["approver"] = "Aprovador";
$strings["error_database"] = "Não foi possível estabelecer uma conexão com o banco de dados";
$strings["error_server"] = "Não foi possível estabelecer uma conexão com o servidor";
$strings["version_control"] = "Controle de Versão";
$strings["vc_status"] = "Status";
$strings["vc_last_in"] = "Data da última modificação";
$strings["ifa_comments"] = "Comentários de aprovação";
$strings["ifa_command"] = "Alterar status da aprovação";
$strings["vc_version"] = "Versão";
$strings["ifc_revisions"] = "Revisão dos Colegas";
$strings["ifc_revision_of"] = "Revisão da versão";
$strings["ifc_add_revision"] = "Adicionar Revisão";
$strings["ifc_update_file"] = "Atualizar Arquivo";
$strings["ifc_last_date"] = "Data da última modificação";
$strings["ifc_version_history"] = "Histórico da Versão";
$strings["ifc_delete_file"] = "Excluir arquivo e todas as suas versões e revisões";
$strings["ifc_delete_version"] = "Excluir versão selecionada";
$strings["ifc_delete_review"] = "Excluir a revisão selecionada";
$strings["ifc_no_revisions"] = "Não há revisões para este documento";
$strings["unlink_files"] = "Remover relação entre os arquivos";
$strings["remove_team"] = "Remover membros da equipe";
$strings["remove_team_info"] = "Remover estes usuários da equipe do projeto?";
$strings["remove_team_client"] = "Remover permissões para visualizar o site do projeto";
$strings["note"] = "Anotação";
$strings["notes"] = "Anotações";
$strings["subject"] = "Assunto";
$strings["delete_note"] = "Excluir Anotação";
$strings["add_note"] = "Adicionar Anotação";
$strings["edit_note"] = "Editar Anotação";
$strings["version_increm"] = "Seleciona a mudança de versão para aplicar:";
$strings["url_dev"] = "URL de Desenvolvimento";
$strings["url_prod"] = "URL de Produção";
$strings["note_owner"] = "Você só pode alterar suas próprias anotações.";
$strings["alpha_only"] = "Somente caracteres alpha-numéricos são permitidos";
$strings["edit_notifications"] = "Editar Notifications Por Email ";
$strings["edit_notifications_info"] = "Selecione os eventos nos quais você gostaria de receber notificações por email.";
$strings["select_deselect"] = "Selecionar/Deselecionar Tudo";
$strings["noti_addprojectteam1"] = "Adicionado à equipe do projeto:";
$strings["noti_addprojectteam2"] = "Você foi adicionado à equipe do projeto por:";
$strings["noti_removeprojectteam1"] = "Removido da equipe do projeto:";
$strings["noti_removeprojectteam2"] = "Você foi removido da equipe do projeto por:";
$strings["noti_newpost1"] = "Nova mensagem:";
$strings["noti_newpost2"] = "Uma nova mensagem foi adicionada ao seguinte debate:";
$strings["edit_noti_taskassignment"] = "Uma nova tarefa foi atribuída a mim.";
$strings["edit_noti_statustaskchange"] = "O status de uma de minhas tarefas mudou.";
$strings["edit_noti_prioritytaskchange"] = "A prioridade de uma de minhas tarefas mudou.";
$strings["edit_noti_duedatetaskchange"] = "A data de término de uma de minhas tarefas mudou.";
$strings["edit_noti_addprojectteam"] = "Eu fui adicionado à uma equipe de projeto.";
$strings["edit_noti_removeprojectteam"] = "Eu fui removido de uma equipe de projeto.";
$strings["edit_noti_newpost"] = "Uma nova mensagem foi enviada a um debate.";
$strings["add_optional"] = "Adicionar um opcional";
$strings["assignment_comment_info"] = "Adicionar comentários sobre a atribuição desta tarefa";
$strings["my_notes"] = "Minhas Anotações";
$strings["edit_settings"] = "Editar Configurações";
$strings["max_upload"] = "Tamanho máximo de arquivo";
$strings["project_folder_size"] = "Tamanho da pasta do projeto";
$strings["calendar"] = "Calendário";
$strings["date_start"] = "Data Inicial";
$strings["date_end"] = "Data Final";
$strings["time_start"] = "Hora Inicial";
$strings["time_end"] = "Hora Final";
$strings["calendar_reminder"] = "Lembrete";
$strings["shortname"] = "Abreviação";
$strings["calendar_recurring"] = "Evento repete toda semana neste dia";
$strings["edit_database"] = "Editar Banco de Dados";
$strings["noti_newtopic1"] = "Novo Debate:";
$strings["noti_newtopic2"] = "Um novo debate foi adicionado ao projeto:";
$strings["edit_noti_newtopic"] = "Um novo tópico para debate foi criado.";
$strings["today"] = "Hoje";
$strings["previous"] = "Anterior";
$strings["next"] = "Próximo";
$strings["help"] = "Ajuda";
$strings["complete_date"] = "Data de Término";
$strings["scope_creep"] = "Dias em Atraso";
$strings["days"] = "Dias";
$strings["logo"] = "Logotipo";
$strings["remember_password"] = "Lembrar Senha";
$strings["client_add_task_note"] = "Nota: A tarefa incluída está registrada na base de dados e aparece aqui, mas somente se delegada a um membro da equipe!";
$strings["noti_clientaddtask1"] = "Tarefa adicionada pelo cliente:";
$strings["noti_clientaddtask2"] = "Uma nova tarefa foi adicionada por um cliente pelo site do projeto:";
$strings["phase"] = "Fase";
$strings["phases"] = "Fases";
$strings["phase_id"] = "ID da Fase";
$strings["current_phase"] = "Fase(s) Ativa(s)";
$strings["total_tasks"] = "Total de Tarefas";
$strings["uncomplete_tasks"] = "Tarefas Pendentes";
$strings["no_current_phase"] = "Atualmente não há fases ativas";
$strings["true"] = "Sim";
$strings["false"] = "Não";
$strings["enable_phases"] = "Ativar Fases";
$strings["phase_enabled"] = "Fase Ativa";
$strings["order"] = "Ordem";
$strings["options"] = "Opções";
$strings["support"] = "Suporte";
$strings["support_request"] = "Pedido de Suporte";
$strings["support_requests"] = "Pedidos de Suporte";
$strings["support_id"] = "ID do Suporte";
$strings["my_support_request"] = "Meus Pedidos de Suporte";
$strings["introduction"] = "Introdução";
$strings["submit"] = "Enviar";
$strings["support_management"] = "Gerenciamento de Suporte";
$strings["date_open"] = "Data de Abertura";
$strings["date_close"] = "Data de Encerramento";
$strings["add_support_request"] = "Adicionar Pedido de Suporte";
$strings["add_support_response"] = "Adicionar Resposta de Suporte";
$strings["respond"] = "Responder";
$strings["delete_support_request"] = "Pedido de suporte excluído";
$strings["delete_request"] = "Excluir pedido de suporte";
$strings["delete_support_post"] = "Excluir mensagem de suporte";
$strings["new_requests"] = "Novos Pedidos";
$strings["open_requests"] = "Pedidos Abertos";
$strings["closed_requests"] = "Pedidos Encerrados";
$strings["manage_new_requests"] = "Gerenciar novos pedidos";
$strings["manage_open_requests"] = "Gerenciar pedidos abertos";
$strings["manage_closed_requests"] = "Gerenciar pedidos encerrados";
$strings["responses"] = "Respostas";
$strings["edit_status"] = "Editar Status";
$strings["noti_support_request_new2"] = "Você enviou um pedido de suporte relativo a: ";
$strings["noti_support_post2"] = "Uma nova respota foi adicionada ao seu pedido de suporte. Por favor verifique os detalhes abaixo.";
$strings["noti_support_status2"] = "Seu pedido de suporte foi atualizado. Por favor verifique os detalhes abaixo.";
$strings["noti_support_team_new2"] = "Um novo pedido de suporte foi adicionado ao projeto: ";
//2.0
$strings["delete_subtasks"] = "Excluir Sub-Tarefas";
$strings["add_subtask"] = "Adicionar Sub-Tarefa";
$strings["edit_subtask"] = "Editar Sub-Tarefa";
$strings["subtask"] = "Sub-Tarefa";
$strings["subtasks"] = "Sub-Tarefas";
$strings["show_details"] = "Exibir Detalhes";
$strings["updates_task"] = "Histórico de atualização da tarefa";
$strings["updates_subtask"] = "Histórico de atualização da sub-tarefa";
//2.1
$strings["go_projects_site"] = "Ir ao Site do Projeto";
$strings["bookmark"] = "Favorito";
$strings["bookmarks"] = "Favoritos";
$strings["bookmark_category"] = "Categoria";
$strings["bookmark_category_new"] = "Nova categoria";
$strings["bookmarks_all"] = "Todos";
$strings["bookmarks_my"] = "Meus Favoritos";
$strings["my"] = "Meus";
$strings["bookmarks_private"] = "Privados";
$strings["shared"] = "Compartilhado";
$strings["private"] = "Privado";
$strings["add_bookmark"] = "Adicionar Favorito";
$strings["edit_bookmark"] = "Editar Favorito";
$strings["delete_bookmarks"] = "Excluir Favoritos";
$strings["team_subtask_details"] = "Detalhes da Sub-Tarefa da Equipe";
$strings["client_subtask_details"] = "Detalhes da Sub-Tarefa do Cliente";
$strings["client_change_status_subtask"] = "Altere seu status abaixo quando você encerrar esta sub-tarefa";
$strings["disabled_permissions"] = "Conta Desabilitada";
$strings["user_timezone"] = "Fuso Horário (GMT)";
//2.2
$strings["project_manager_administrator_permissions"] = "Diretor de Projeto";
$strings["bug"] = "Acompanhamento de Bugs";
//2.3
$strings["report"] = "Relatar";
$strings["license"] = "Licença";
//2.4
$strings["settings_notwritable"] = "O arquivo Settings.php não pode ser modificado";
//2.5
$strings["invoicing"] = "Faturando";  // revisar
$strings["invoice"] = "Fatura";
$strings["invoices"] = "Faturas";
$strings["date_invoice"] = "Data da fatura";
$strings["header_note"] = "Nota de cabeçalho";
$strings["footer_note"] = "Nota de rodapé";
$strings["total_ex_tax"] = "Total da taxa passada";
$strings["total_inc_tax"] = "Total da taxa incorporada";
$strings["tax_rate"] = "Porcentagem de tributação";
$strings["tax_amount"] = "Quantia da taxa";
$strings["invoice_items"] = "Itens da fatura";
$strings["amount_ex_tax"] = "Quantidade da taxa passada";
$strings["completed"] = "Completa";
$strings["service"] = "Serviço";
$strings["name_print"] = "Nome impresso";
$strings["edit_invoice"] = "Editar fatura";
$strings["edit_invoiceitem"] = "Editar item da fatura";
$strings["calculation"] = "Cálculo";
$strings["items"] = "Itens";
$strings["position"] = "Posição";
$strings["service_management"] = "Administração de Serviço";
$strings["hourly_rate"] = "Preço por Hora";
$strings["add_service"] = "Adicionar Serviço";
$strings["edit_service"] = "Editar Serviço";
$strings["delete_services"] = "Excluir Serviços";
$strings["worked_hours"] = "Horas Trabalhadas";
$strings["rate_type"] = "Tipo de Taxa";
$strings["rate_value"] = "Valor da Taxa";
$strings["note_invoice_items_notcompleted"] = "Nem todos os itens estão completos";

$rateType = array(
    0 => "Taxa personalizada",
    1 => "Taxa por projeto",
    2 => "Taxa por organização",
    3 => "Taxa por serviço"
);

//HACKS

$strings["newsdesk"] = "Painel de Notícias";
$strings["newsdesk_list"] = "Lista de notícias";
$strings["article_newsdesk"] = "Corpo da Notícia";
$strings["update_newsdesk"] = "Atualizar Notícias";
$strings["my_newsdesk"] = "Meu Painel de Notícias";
$strings["edit_newsdesk"] = "Editar Artigo";
$strings["copy_newsdesk"] = "Copiar Artigo";
$strings["add_newsdesk"] = "Adicionar Artigo";
$strings["del_newsdesk"] = "Excluir Artigo";
$strings["delete_news_note"] = "Nota: Isto também irá excluir todos os comentários do artigo selecionado";
$strings["author"] = "Autor";
$strings["blank_newsdesk_title"] = "Título em Branco";
$strings["blank_newsdesk"] = "As notícias não puderam ser localizadas.";
$strings["blank_newsdesk_comment"] = "Comentário em Branco";
$strings["remove_newsdesk"] = "Os artigos e seus comentários foram excluídos com sucesso";
$strings["add_newsdesk_comment"] = "Adicionar Comentário ao Artigo";
$strings["edit_newsdesk_comment"] = "Editar Comentário do Artigo";
$strings["del_newsdesk_comment"] = "Excluir Comentário(s) do Artigo";
$strings["remove_newsdesk_comment"] = "O comentário para o artigo foi excluído com sucesso";
$strings["errorpermission_newsdesk"] = "Você não tem permissão para modificar ou cancelar esta notícia";
$strings["errorpermission_newsdesk_comment"] = "Você não tem permissão para modificar ou excluir este comentário";
$strings["newsdesk_related"] = "Projeto Relacionado";
$strings["newsdesk_related_generic"] = "Nenhum";
$strings["newsdesk_related_links"] = "Links Relacionados";
$strings["newsdesk_rss"] = "Ativar RSS";
$strings["newsdesk_rss_enabled"] = "RSS Ativo";

$strings["noti_memberactivation1"] = "Conta Ativada";
$strings["noti_memberactivation2"] = "You have just been added into the phpCollab client management system.  This system has been developed and is continually being upgraded in order to help you, the client, keep tabs on the progress of your project.\n\nTo enter the system, point your browser (preferably Internet Explorer 6.x or Netscape Navigator 7.x) to $root and enter the following:";
$strings["noti_memberactivation3"] = "Nome de Usuário:";
$strings["noti_memberactivation4"] = "Senha:";
$strings["noti_memberactivation5"] = "Once you have typed the information above and pressed \"enter\" you will be allowed to access  your account. \n\nIn tandem with this email, you will receive additional messages regarding activations, task submissions, and other events relating to your account.  These emails have been sent to keep you informed on the progress of your project.";

//BEGIN email project users mod
$strings["email_users"] = "Email Users";
$strings["email_following"] = "Email Following";
$strings["email_sent"] = "Seu email foi enviado com sucesso.";
//END email project users mod

$strings["clients_connected"] = "(Site do Projeto)";

//2.5rc3
$strings["my_subtasks"] = "Minhas Subtarefas";
