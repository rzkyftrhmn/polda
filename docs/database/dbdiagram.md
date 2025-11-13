// Creating tables
Table users as U {
  id int [pk, increment] // auto-increment
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
  type varchar // polda, polres
  created_at timestamp
  updated_at timestamp
}

Table reports {
  id int [pk, increment] // auto-increment
  title varchar
  incident_datetime datetime
  province_id int
  city_id int
  district_id int
  address_detail text
  category_id int
  status enum // PEMERIKSAAN, LIMPAH, SIDANG, SELESAI
  description text
  created_at timestamp
  updated_at timestamp
}

Table report_journeys {
  id int [pk, increment] // auto-increment
  report_id int
  institution_id int
  division_id int
  type varchar // PEMERIKSAAN, LIMPAH, SIDANG
  description text
  created_at timestamp
  updated_at timestamp
}

Table report_evidences {
  id int [pk, increment] // auto-increment
  report_journey_id int
  file_url varchar
  file_type varchar
  created_at timestamp
  updated_at timestamp
}

Table suspects {
  id int [pk, increment] // auto-increment
  report_id int
  name varchar
  description varchar
  created_at timestamp
  updated_at timestamp
}

Table provinces {
  id int [pk, increment] // auto-increment
  name varchar
  created_at timestamp
  updated_at timestamp
}

Table cities {
  id int [pk, increment] // auto-increment
  province_id int
  name varchar
  created_at timestamp
  updated_at timestamp
}

Table districts {
  id int [pk, increment] // auto-increment
  city_id int
  name varchar
  created_at timestamp
  updated_at timestamp
}

Table report_categories {
  id int [pk, increment] // auto-increment
  name varchar
  created_at timestamp
  updated_at timestamp
}

Ref: "users"."id" < "model_has_permissions"."model_id"

Ref: "permissions"."id" < "model_has_permissions"."permission_id"

Ref: "users"."id" < "model_has_roles"."model_id"

Ref: "roles"."id" < "model_has_roles"."role_id"

Ref: "permissions"."id" < "role_has_permissions"."permission_id"

Ref: "roles"."id" < "role_has_permissions"."role_id"

Ref: "users"."division_id" < "divisions"."id"

Ref: "institutions"."id" < "users"."institution_id"

Ref: "reports"."id" < "suspects"."report_id"

Ref: "provinces"."id" < "reports"."province_id"

Ref: "cities"."id" < "reports"."city_id"

Ref: "districts"."id" < "reports"."district_id"

Ref: "report_categories"."id" < "reports"."category_id"

Ref: "reports"."id" < "report_journeys"."report_id"

Ref: "report_journeys"."id" < "report_evidences"."report_journey_id"

Ref: "divisions"."id" < "report_journeys"."division_id"

Ref: "institutions"."id" < "report_journeys"."institution_id"