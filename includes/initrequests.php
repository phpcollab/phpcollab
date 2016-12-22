<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/initrequests.php

$initrequest["sorting"] = "SELECT *
FROM " . $tableCollab["sorting"] . " sor
";

$initrequest["services"] = "SELECT *
FROM " . $tableCollab["services"] . " serv
";

$initrequest["invoices_items"] = "SELECT invitem.*
FROM " . $tableCollab["invoices_items"] . " invitem
LEFT OUTER JOIN " . $tableCollab["invoices"] . " inv ON inv.id = invitem.invoice
";

$initrequest["invoices"] = "SELECT inv.*,pro.id,pro.name
FROM " . $tableCollab["invoices"] . " inv
LEFT OUTER JOIN " . $tableCollab["projects"] . " pro ON pro.id = inv.project
";

$initrequest["calendar"] = "SELECT cal.*,mem.email_work, mem.name 
FROM " . $tableCollab["calendar"] . " cal
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = cal.owner
";

$initrequest["notes"] = "SELECT note.id, note.project, note.owner, note.topic, note.subject, note.description, note.date, note.published, mem.id, mem.login, mem.name, mem.email_work
FROM " . $tableCollab["notes"] . " note
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = note.owner
LEFT OUTER JOIN " . $tableCollab["projects"] . " pro ON pro.id = note.project
";

$initrequest["logs"] = "SELECT log.id, log.login, log.password, log.ip, log.session, log.compt, log.last_visite, log.connected, mem.profil
FROM " . $tableCollab["logs"] . " log
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.login = log.login
";

$initrequest["notifications"] = "SELECT noti.*,mem.id,mem.login,mem.name,mem.email_work,mem.organization,mem.profil
FROM " . $tableCollab["notifications"] . " noti
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = noti.member
";

$initrequest["members"] = <<<SQL
SELECT
mem.id AS mem_id,
mem.organization AS mem_organization,
mem.login AS mem_login,
mem.password AS mem_password,
mem.name AS mem_name,
mem.title AS mem_title,
mem.email_work AS mem_email_work,
mem.email_home AS mem_email_home,
mem.phone_work AS mem_phone_work,
mem.phone_home AS mem_phone_home,
mem.mobile AS mem_mobile,
mem.fax AS mem_fax,
mem.comments AS mem_comments,
mem.profil AS mem_profil,
mem.created AS mem_created,
mem.logout_time AS mem_logout_time,
mem.last_page AS mem_last_page,
mem.timezone AS mem_timezone,
org.id AS org_id,
org.name AS org_name,
log.connected AS log_connected,
log.login AS log_login
FROM members mem
LEFT OUTER JOIN organizations org ON org.id = mem.organization
LEFT OUTER JOIN logs log ON log.login = mem.login
SQL;



$initrequest["projects"] = <<<SQL
SELECT 
pro.id AS pro_id, 
pro.organization AS pro_organization, 
pro.owner AS pro_owner, 
pro.priority AS pro_priority, 
pro.status AS pro_status, 
pro.name AS pro_name, 
pro.description AS pro_description, 
pro.url_dev AS pro_url_dev, 
pro.url_prod AS pro_url_prod, 
pro.created AS pro_created, 
pro.modified AS pro_modified, 
pro.published AS pro_published, 
pro.upload_max AS pro_upload_max, 
pro.phase_set AS pro_phase_set, 
pro.invoicing AS pro_invoicing, 
pro.hourly_rate AS pro_hourly_rate, 
org.id AS pro_org_id, 
org.name AS pro_org_name, 
mem.id AS pro_mem_id, 
mem.login AS pro_mem_login, 
mem.name AS pro_mem_name, 
mem.email_work AS pro_mem_email_work
FROM projects pro
LEFT OUTER JOIN organizations org ON org.id = pro.organization
LEFT OUTER JOIN members mem ON mem.id = pro.owner
SQL;

$initrequest["files"] = "SELECT fil.*, mem.id, mem.login, mem.name, mem.email_work, mem2.id, mem2.login, mem2.name, mem2.email_work
FROM " . $tableCollab["files"] . " fil
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = fil.owner
LEFT OUTER JOIN " . $tableCollab["members"] . " mem2 ON mem2.id = fil.approver
";

$initrequest["organizations"] = <<<SQL
SELECT 
    org.id as org_id,
    org.name as org_name,
    org.address1 as org_address1,
    org.address2 as org_address2,
    org.zip_code as org_zip_code,
    org.city as org_city,
    org.country as org_country,
    org.phone as org_phone,
    org.fax as org_fax,
    org.url as org_url,
    org.email as org_email,
    org.comments as org_comments,
    org.created as org_created,
    org.extension_logo as org_extension_logo,
    org.owner as org_owner,
    org.hourly_rate as org_hourly_rate,
    mem.id as mem_id,
    mem.login as mem_login,
    mem.name as mem_name,
    mem.email_work as mem_email_work
FROM {$tableCollab["organizations"]} org
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = org.owner
SQL;

$initrequest["topics"] = "SELECT topic.id, topic.project, topic.owner, topic.subject, topic.status, topic.last_post, topic.posts, topic.published, mem.id, mem.login, mem.name, mem.email_work, pro.id, pro.name
FROM " . $tableCollab["topics"] . " topic
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = topic.owner
LEFT OUTER JOIN " . $tableCollab["projects"] . " pro ON pro.id = topic.project
";

$initrequest["posts"] = "SELECT pos.id, pos.topic, pos.member, pos.created, pos.message, mem.id, mem.login, mem.name, mem.email_work
FROM " . $tableCollab["posts"] . " pos
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = pos.member
LEFT OUTER JOIN " . $tableCollab["topics"] . " topic ON topic.id = pos.topic
";

$initrequest["assignments"] = <<<ASSIGNMENTS
SELECT 
ass.id as ass_id, 
ass.task as ass_task,
ass.owner as ass_owner,
ass.assigned_to as ass_assigned_to,
ass.comments as ass_comments,
ass.assigned as ass_assigned,
mem1.id as ass_mem1_id,
mem1.login as ass_mem1_login,
mem1.name as ass_mem1_name,
mem1.email_work as ass_mem1_email_work,
mem2.id as ass_mem2_id,
mem2.login as ass_mem2_login,
mem2.name as ass_mem2_name,
mem2.email_work as ass_mem2_email_work
FROM {$tableCollab["assignments"]} ass
LEFT OUTER JOIN {$tableCollab["members"]} mem1 ON mem1.id = ass.owner
LEFT OUTER JOIN {$tableCollab["members"]} mem2 ON mem2.id = ass.assigned_to
ASSIGNMENTS;

$initrequest["reports"] = "SELECT *
FROM " . $tableCollab["reports"] . " rep
";

$initrequest["teams"] =<<<TEAMSSQL
SELECT 
    tea.id as tea_id, 
    tea.project as tea_project, 
    tea.member as tea_member, 
    tea.published as tea_published, 
    tea.authorized as tea_authorized, 
    mem.id as tea_mem_id, 
    mem.login as tea_mem_login, 
    mem.name as tea_mem_name, 
    mem.email_work as tea_mem_email_work, 
    mem.title as tea_mem_title, 
    mem.phone_work as tea_mem_phone_work, 
    org.name as tea_org_name, 
    pro.id as tea_pro_id, 
    pro.name as tea_pro_name, 
    pro.priority as tea_pro_priority, 
    pro.status as tea_pro_status, 
    pro.published as tea_pro_published, 
    org2.name as tea_org2_name, 
    mem2.login as tea_mem2_login, 
    mem2.email_work as tea_mem2_email_work, 
    org2.id as tea_org2_id, 
    log.connected as tea_log_connected, 
    mem.profil as tea_mem_profile, 
    mem.password as tea_mem_password
FROM {$tableCollab["teams"]} tea
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = tea.member
LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = tea.project
LEFT OUTER JOIN {$tableCollab["organizations"]} org ON org.id = mem.organization
LEFT OUTER JOIN {$tableCollab["organizations"]} org2 ON org2.id = pro.organization
LEFT OUTER JOIN {$tableCollab["members"]} mem2 ON mem2.id = pro.owner
LEFT OUTER JOIN {$tableCollab["logs"]} log ON log.login = mem.login
TEAMSSQL;


$initrequest["tasks"] = <<<TASKSSQL
SELECT 
    tas.id as tas_id,
    tas.project as tas_project,
    tas.priority as tas_priority,
    tas.status as tas_status,
    tas.owner as tas_owner,
    tas.assigned_to as tas_assigned_to,
    tas.name as tas_name,
    tas.description as tas_description,
    tas.start_date as tas_start_date,
    tas.due_date as tas_due_date,
    tas.estimated_time as tas_estimated_time,
    tas.actual_time as tas_actual_time,
    tas.comments as tas_comments,
    tas.completion as tas_completion,
    tas.created as tas_created,
    tas.modified as tas_modified,
    tas.assigned as tas_assigned,
    tas.published as tas_published,
    tas.parent_phase as tas_parent_phase,
    tas.complete_date as tas_complete_date,
    tas.invoicing as tas_invoicing,
    tas.worked_hours as tas_worked_hours,
    mem.id as tas_mem_id, 
    mem.name as tas_mem_name, 
    mem.login as tas_mem_login, 
    mem.email_work as tas_mem_email_work, 
    mem2.id as tas_mem2_id, 
    mem2.name as tas_mem2_name, 
    mem2.login as mem2_login, 
    mem2.email_work as tas_mem2_email_work, 
    mem.organization as tas_mem_organization, 
    pro.name as tas_pro_name, 
    org.id as tas_org_id
FROM {$tableCollab["tasks"]} tas
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = tas.assigned_to
LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = tas.project
LEFT OUTER JOIN {$tableCollab["members"]} mem2 ON mem2.id = tas.owner
LEFT OUTER JOIN {$tableCollab["organizations"]} org ON org.id = pro.organization
TASKSSQL;

$initrequest["subtasks"] = <<<SQL
SELECT 
  subtas.id as subtas_id,
  subtas.task as subtas_task,
  subtas.priority as subtas_priority,
  subtas.status as subtas_status,
  subtas.owner as subtas_owner,
  subtas.assigned_to as subtas_assigned_to,
  subtas.name as subtas_name,
  subtas.description as subtas_description,
  subtas.start_date as subtas_start_date,
  subtas.due_date as subtas_due_date,
  subtas.estimated_time as subtas_estimated_time,
  subtas.actual_time as subtas_actual_time,
  subtas.comments as subtas_comments,
  subtas.completion as subtas_completion,
  subtas.created as subtas_created,
  subtas.modified as subtas_modified,
  subtas.assigned as subtas_assigned,
  subtas.published as subtas_published,
  subtas.complete_date as subtas_complete_date,
  mem.id as subtas_mem_id, 
  mem.name as subtas_mem_name, 
  mem.login as subtas_mem_login, 
  mem.email_work as subtas_mem_email_work, 
  mem2.id as subtas_mem2_id, 
  mem2.name as subtas_mem2_name, 
  mem2.login as subtas_mem2_login, 
  mem2.email_work as subtas_mem2_email_work, 
  mem.organization as subtas_mem_organization, 
  tas.name as subtas_tas_name
FROM subtasks subtas
LEFT OUTER JOIN members mem ON mem.id = subtas.assigned_to
LEFT OUTER JOIN tasks tas ON tas.id = subtas.task
LEFT OUTER JOIN members mem2 ON mem2.id = subtas.owner
SQL;

$initrequest["phases"] = <<<PHASESSQL
SELECT 
pha.id as pha_id, 
pha.project_id as pha_project_id, 
pha.order_num as pha_order_num, 
pha.status as pha_status, 
pha.name as pha_name, 
pha.date_start as pha_date_start, 
pha.date_end as pha_date, 
pha.comments as pha_comments
FROM {$tableCollab["phases"]} pha
PHASESSQL;

$initrequest["updates"] = <<<UPDATES
SELECT 
upd.id as upd_id,
upd.type as upd_type,
upd.item as upd_item,
upd.member as upd_member,
upd.comments as upd_comments,
upd.created as upd_created,
mem.id as upd_mem_id,
mem.name as upd_mem_name,
mem.login as upd_mem_login,
mem.email_work as upd_mem_email_work
FROM {$tableCollab["updates"]} upd
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = upd.member
UPDATES;

$initrequest["support_requests"] = "SELECT sr.id, sr.status, sr.member, sr.priority, sr.subject, sr.message, sr.owner, sr.date_open, sr.date_close, sr.project, pro.name, mem.name, mem.email_work
FROM " . $tableCollab["support_requests"] . " sr
LEFT OUTER JOIN " . $tableCollab["projects"] . " pro ON pro.id = sr.project
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = sr.member
";

$initrequest["support_posts"] = "SELECT sp.id, sp.request_id, sp.message, sp.date, sp.owner, sp.project, mem.name, mem.email_work
FROM " . $tableCollab["support_posts"] . " sp
LEFT OUTER JOIN " . $tableCollab["members"] . " mem ON mem.id = sp.owner
";

$initrequest["bookmarks"] = <<<SQL
SELECT
  boo.id AS boo_id,
  boo.owner AS boo_owner,
  boo.category AS boo_category,
  boo.name AS boo_name,
  boo.url AS boo_url,
  boo.description AS boo_description,
  boo.shared AS boo_shared,
  boo.home AS boo_home,
  boo.comments AS boo_comments,
  boo.users AS boo_users,
  boo.created AS boo_created,
  boo.modified AS boo_modified,
  mem.id AS boo_mem_id,
  mem.login AS boo_mem_login, 
  mem.email_work AS boo_mem_email_work, 
  boocat.name AS boo_boocat_name
FROM bookmarks boo
LEFT OUTER JOIN bookmarks_categories boocat ON boocat.id = boo.category
LEFT OUTER JOIN members mem ON mem.id = boo.owner
SQL;

$initrequest["bookmarks_categories"] = <<<SQL
SELECT
boocat.id AS boocat_id, 
boocat.name AS boocat_name, 
boocat.description AS boocat_description 
FROM bookmarks_categories boocat
SQL;

$initrequest["newsdeskposts"] = "SELECT news.* FROM " . $tableCollab["newsdeskposts"] . " news ";

$initrequest["newsdeskcomments"] = "SELECT newscom.* FROM " . $tableCollab["newsdeskcomments"] . " newscom ";

?>