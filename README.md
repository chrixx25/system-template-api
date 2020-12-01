**LOGIN**
;;;
{
method: "POST",
, url: "{{base_url}}login",
, Body: {
user
, pass
}
"dataType": "JSON"
}
;;;

**GetMYAttribute**
{
url : "{{base_url}}task/getMyAttribute"
,header : {
Authorization: access_token
}
, dataType: "JSON"
;;;

**getTaskHistory**
;;;
$status	    Description
	1			NEW
	2			WIP
	3			SUBMITTED
	4			DONE
	5			DELETED
{
method: "POST"
, url: "{{base_url}}task/getTaskHistory"
, Body: {
	$status
, $page
	, $limit
, $search
}
;;;
**countTaskHistoryByStatus**
;;;
{
method: "GET",
, url: "{{base_url}}task/countTaskHistoryByStatus"
, Body : {
	$status
}
;;;
**INSERT NEW TASK**
;;;
method: "POST"
, url: {{base_url}}task/insertTaskHistory
, Body: {
"data": [ 'id' : 1 , 'val' : '0261']
, "assigned_to": 1
, "assigned_by" : 2
, "task_id" : 1
, "due_date" : "2019-10-26"
, "start_time": NULL
, "start_end": NULL
, "type_task": 0
}
;;;
**UPDATE TASK**
;;;
method: PATCH
, url: {{base_url}}task/updateTaskHistory
, Body: {
"data": [ 'id' : 1 , 'val' : '0261']
, "assigned_to": 1
, "assigned_by" : 2
, "task_id" : 1
, "due_date" : "2019-10-26"
}
;;;
**START TASK**
;;;
method: POST
, url: {{base_url}}task/startTask
, Body: { "task_id" : 1
}
;;;
**DONE TASK**
;;;
method: POST
, url: {{base_url}}task/doneTask
, Body: { "task_id" : 1
, "created_by": 4
}
;;;
**DONE TASK**
;;;
method: PATCH
, url: {{base_url}}task/deleteTask
, x-www-form-urlencoded: { "task_id" : 1
}
;;;
**RETURN TASK**
;;;
method: POST
, url: {{base_url}}task/returnTask
, body: {
"task_id" : 1
, "remarks" : test
, "return_date" : 2019-10-30
, "created_by" : 4
}
;;;
**STOP TASK**
;;;
method: POST
, url: {{base_url}}task/stopTask
, body: {
"task_id" : 1
}
;;;
**SUBMIT TASK**
;;;
method: POST
, url: {{base_url}}task/submitTask
, body: {
"task_id" : 1
}
;;;
