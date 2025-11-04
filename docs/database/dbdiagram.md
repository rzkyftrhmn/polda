// Creating tables
Table users as U {
  id int [pk, increment] // auto-increment
  uuid varchar
  institution_id int
  division_id int
  username varchar
  name varchar
  email varchar
  email_verified_at timestamp
  password varchar
  remember_token varchar
  created_at timestamp
  updated_at timestamp
}

Table roles {
  id int [pk, increment] // auto-increment
  name varchar
  guard_name varchar
  created_at timestamp
  updated_at timestamp
}

Table role_has_permissions {
  permission_id int
  role_id int
}

Table permissions {
  id int [pk, increment] // auto-increment
  permissions varchar
  guard_name varchar
  created_at timestamp
  updated_at timestamp
}

Table model_has_roles {
  id int [pk, increment] // auto-increment
  role_id int
  model_type varchar
  model_id int
}

Table model_has_permissions {
  permission_id int
  model_type varchar
  model_id int
}

Table institutions {
  id int [pk, increment] // auto-increment
  name varchar
  type varchar // polda, polres
  created_at timestamp
  updated_at timestamp
}

Table divisions {
  id int [pk, increment] // auto-increment
  parent_id int
  name varchar
  created_at timestamp
  updated_at timestamp
}

Table complaints {
  id int [pk, increment] // auto-increment
  suspect_name varchar
  victim_name varchar
  victim_address text
  victim_dob date
  victim_loss text
  detail text
  status varchar
  created_at timestamp
  updated_at timestamp
}

Table complaint_journeys {
  id int [pk, increment] // auto-increment
  complaint_id int
  division_id_from int
  division_id_to int
  part varchar
  status int
  created_at timestamp
  updated_at timestamp
}

Table complaint_files {
  id int [pk, increment] // auto-increment
  complaint_id int
  journey_id int
  name varchar
  path varchar
  created_at timestamp
  updated_at timestamp
}

Ref: "users"."id" < "model_has_permissions"."model_id"

Ref: "permissions"."id" < "model_has_permissions"."permission_id"

Ref: "users"."id" < "model_has_roles"."model_id"

Ref: "roles"."id" < "model_has_roles"."role_id"

Ref: "permissions"."id" < "role_has_permissions"."permission_id"

Ref: "roles"."id" < "role_has_permissions"."role_id"

Ref: "complaints"."id" < "complaint_files"."complaint_id"

Ref: "complaint_journeys"."id" < "complaint_files"."journey_id"

Ref: "divisions"."id" < "complaint_journeys"."division_id_from"

Ref: "divisions"."id" < "complaint_journeys"."division_id_to"

Ref: "users"."division_id" < "divisions"."id"

Ref: "institutions"."id" < "users"."institution_id"