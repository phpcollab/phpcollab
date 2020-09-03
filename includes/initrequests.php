<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/initrequests.php

$tableCollab = $GLOBALS["tableCollab"];

$initrequest["sorting"] = "SELECT *
FROM {$tableCollab["sorting"]} sor
";

$initrequest["services"] = <<<SERVICES
SELECT 
    serv.id AS serv_id,
    serv.name AS serv_name,
    serv.name_print AS serv_name_print,
    serv.hourly_rate AS serv_hourly_rate
FROM {$tableCollab["services"]} serv
SERVICES;

$initrequest["invoices_items"] = <<<INVOICE_ITEMS
SELECT 
    invitem.id AS invitem_id,
    invitem.invoice AS invitem_invoice,
    invitem.position AS invitem_position,
    invitem.mod_type AS invitem_mod_type,
    invitem.mod_value AS invitem_mod_value,
    invitem.title AS invitem_title,
    invitem.description AS invitem_description,
    invitem.worked_hours AS invitem_worked_hours,
    invitem.amount_ex_tax AS invitem_amount_ex_tax,
    invitem.rate_type AS invitem_rate_type,
    invitem.rate_value AS invitem_rate_value,
    invitem.status AS invitem_status,
    invitem.active AS invitem_active,
    invitem.completed AS invitem_completed,
    invitem.created AS invitem_created,
    invitem.modified AS invitem_modified
FROM {$tableCollab["invoices_items"]} invitem
LEFT OUTER JOIN {$tableCollab["invoices"]} inv ON inv.id = invitem.invoice
INVOICE_ITEMS;

$initrequest["invoices"] = <<<INVOICES
SELECT 
    inv.id AS inv_id,
    inv.project AS inv_project,
    inv.header_note AS inv_header_note,
    inv.footer_note AS inv_footer_note,
    inv.date_sent AS inv_date_sent,
    inv.due_date AS inv_due_date,
    inv.total_ex_tax AS inv_total_ex_tax,
    inv.tax_rate AS inv_tax_rate,
    inv.tax_amount AS inv_tax_amount,
    inv.total_inc_tax AS inv_total_inc,
    inv.status AS inv_status,
    inv.active AS inv_active,
    inv.created AS inv_created,
    inv.modified AS inv_modified,
    inv.published AS inv_published,
    pro.id AS inv_pro_id,
    pro.name AS inv_pro_name
FROM {$tableCollab["invoices"]} inv
LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = inv.project
INVOICES;

$initrequest["calendar"] = <<<CALENDAR
SELECT 
cal.id as cal_id,
cal.owner as cal_owner,
cal.subject as cal_subject,
cal.description as cal_description,
cal.shortname as cal_shortname,
cal.date_start as cal_date_start,
cal.date_end as cal_date_end,
cal.time_start as cal_time_start,
cal.time_end as cal_time_end,
cal.reminder as cal_reminder,
cal.recurring as cal_recurring,
cal.recur_day as cal_recur_day,
cal.broadcast as cal_broadcast,
cal.location as cal_location,
mem.email_work as cal_mem_email_work, 
mem.name as cal_mem_name 
FROM {$tableCollab["calendar"]} cal
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = cal.owner
CALENDAR;

$initrequest["notes"] = <<<NOTES
SELECT 
    note.id AS note_id, 
    note.project AS note_project, 
    note.owner AS note_owner, 
    note.topic AS note_topic, 
    note.subject AS note_subject, 
    note.description AS note_description, 
    note.date AS note_date, 
    note.published AS note_published, 
    mem.id AS note_mem_id, 
    mem.login AS note_mem_login, 
    mem.name AS note_mem_name,
    mem.email_work AS note_mem_email_work
FROM {$tableCollab["notes"]} note
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = note.owner
LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = note.project
NOTES;


$initrequest["logs"] = "SELECT log.id, log.login, log.password, log.ip, log.session, log.compt, log.last_visite, log.connected, mem.profil
FROM {$tableCollab["logs"]} log
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.login = log.login
";

$initrequest["notifications"] = "SELECT noti.*,mem.id,mem.login,mem.name,mem.email_work,mem.organization,mem.profil
FROM {$tableCollab["notifications"]} noti
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = noti.member
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
FROM {$tableCollab["members"]} mem
LEFT OUTER JOIN {$tableCollab["organizations"]} org ON org.id = mem.organization
LEFT OUTER JOIN {$tableCollab["logs"]} log ON log.login = mem.login
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
FROM {$tableCollab["projects"]} pro
LEFT OUTER JOIN {$tableCollab["organizations"]} org ON org.id = pro.organization
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = pro.owner
SQL;

$initrequest["files"] = <<<FILES_SQL
SELECT 
    fil.id AS fil_id,
    fil.owner AS fil_owner,
    fil.project AS fil_project,
    fil.task AS fil_task,
    fil.name AS fil_name,
    fil.date AS fil_date,
    fil.size AS fil_size,
    fil.extension AS fil_extension,
    fil.comments AS fil_comments,
    fil.comments_approval AS fil_comments_approval,
    fil.approver AS fil_approver,
    fil.date_approval AS fil_date_approval,
    fil.upload AS fil_upload,
    fil.published AS fil_published,
    fil.status AS fil_status,
    fil.vc_status AS fil_vc_status,
    fil.vc_version AS fil_vc_version,
    fil.vc_parent AS fil_vc_parent,
    fil.phase AS fil_phase,
    mem.id AS fil_mem_id, 
    mem.login AS fil_mem_login, 
    mem.name AS fil_mem_name, 
    mem.email_work AS fil_mem_email_work, 
    mem2.id AS fil_mem2_id, 
    mem2.login AS fil_mem2_login, 
    mem2.name AS fil_mem2_name, 
    mem2.email_work AS fil_mem2_email_work
    FROM {$tableCollab["files"]} fil
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = fil.owner
LEFT OUTER JOIN {$tableCollab["members"]} mem2 ON mem2.id = fil.approver
FILES_SQL;

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

$initrequest["topics"] = <<<TOPICS
SELECT 
    topic.id AS top_id, 
    topic.project AS top_project, 
    topic.owner AS top_owner, 
    topic.subject AS top_subject, 
    topic.status AS top_status, 
    topic.last_post AS top_last_post, 
    topic.posts AS top_posts, 
    topic.published AS top_published, 
    mem.id AS top_mem_id, 
    mem.login AS top_mem_login, 
    mem.name AS top_mem_name, 
    mem.email_work AS top_mem_email_work, 
    pro.id AS top_pro_id, 
    pro.name AS top_pro_name
FROM {$tableCollab["topics"]} topic
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = topic.owner
LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = topic.project
TOPICS;

$initrequest["posts"] = "SELECT 
  pos.id AS pos_id, 
  pos.topic AS pos_topic, 
  pos.member AS pos_member, 
  pos.created AS pos_created, 
  pos.message AS pos_message, 
  mem.id AS pos_mem_id, 
  mem.login AS pos_mem_login, 
  mem.name AS pos_mem_name, 
  mem.email_work AS pos_mem_email_work
FROM {$tableCollab["posts"]} pos
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = pos.member
LEFT OUTER JOIN {$tableCollab["topics"]} topic ON topic.id = pos.topic
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

$initrequest["reports"] = <<<REPORTS
SELECT 
    rep.id as rep_id, 
    rep.owner as rep_owner, 
    rep.name as rep_name, 
    rep.projects as rep_projects, 
    rep.members as rep_members, 
    rep.priorities as rep_priorities, 
    rep.status as rep_status, 
    rep.date_due_start as rep_date_due_end, 
    rep.date_due_end as rep_date_due_end, 
    rep.created as rep_created, 
    rep.date_complete_start as rep_date_complete_start, 
    rep.date_complete_end as rep_date_complete_end, 
    rep.clients as rep_clients 
FROM {$tableCollab["reports"]} rep
REPORTS;

$initrequest["teams"] = <<<TEAMSSQL
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
    mem2.login as tas_mem2_login, 
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
FROM {$tableCollab["subtasks"]} subtas
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = subtas.assigned_to
LEFT OUTER JOIN {$tableCollab["tasks"]} tas ON tas.id = subtas.task
LEFT OUTER JOIN {$tableCollab["members"]} mem2 ON mem2.id = subtas.owner
SQL;

$initrequest["phases"] = <<<PHASESSQL
SELECT 
pha.id as pha_id, 
pha.project_id as pha_project_id, 
pha.order_num as pha_order_num, 
pha.status as pha_status, 
pha.name as pha_name, 
pha.date_start as pha_date_start, 
pha.date_end as pha_date_end, 
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

$initrequest["support_requests"] = <<<SQL
SELECT 
  sr.id AS sr_id, 
  sr.status AS sr_status, 
  sr.member AS sr_member, 
  sr.priority AS sr_priority, 
  sr.subject AS sr_subject, 
  sr.message AS sr_message, 
  sr.owner AS sr_owner, 
  sr.date_open AS sr_date_open, 
  sr.date_close AS sr_date_close, 
  sr.project AS sr_project, 
  pro.name AS sr_pro_name, 
  mem.name AS sr_mem_name, 
  mem.email_work AS sr_mem_email_work
FROM {$tableCollab["support_requests"]} sr
LEFT OUTER JOIN {$tableCollab["projects"]} pro ON pro.id = sr.project
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = sr.member
SQL;

$initrequest["support_posts"] = <<<SQL
SELECT 
  sp.id AS sp_id, 
  sp.request_id AS sp_request_id, 
  sp.message AS sp_message, 
  sp.date AS sp_date, 
  sp.owner AS sp_owner, 
  sp.project AS sp_project, 
  mem.name AS sp_mem_name, 
  mem.email_work AS sp_mem_email_work
FROM {$tableCollab["support_posts"]} sp
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = sp.owner
SQL;

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
FROM {$tableCollab["bookmarks"]} boo
LEFT OUTER JOIN {$tableCollab["bookmarks_categories"]} boocat ON boocat.id = boo.category
LEFT OUTER JOIN {$tableCollab["members"]} mem ON mem.id = boo.owner
SQL;

$initrequest["bookmarks_categories"] = <<<SQL
SELECT
    boocat.id AS boocat_id, 
    boocat.name AS boocat_name, 
    boocat.description AS boocat_description 
FROM {$tableCollab["bookmarks_categories"]} boocat
SQL;

$initrequest["newsdeskposts"] = <<<NEWSDESKPOSTS
SELECT 
    news.id AS news_id,
    news.pdate AS news_date,
    news.title AS news_title,
    news.author AS news_author,
    news.related AS news_related,
    news.content AS news_content,
    news.links AS news_links,
    news.rss AS news_rss
FROM {$tableCollab["newsdeskposts"]} news
NEWSDESKPOSTS;

$initrequest["newsdeskcomments"] = <<<NEWSDESKCOMMENTS
SELECT 
    newscom.id AS newscom_id,
    newscom.post_id AS newscom_post_id,
    newscom.name AS newscom_name,
    newscom.comment AS newscom_comment
FROM {$tableCollab["newsdeskcomments"]} newscom
NEWSDESKCOMMENTS;
