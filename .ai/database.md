# Database Schema

## Table: `admin`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| admin_id | int | NO | PRI | NULL | auto_increment |
| admin_group_id | int | YES |  | NULL |  |
| user_name | varchar(255) | YES | UNI | NULL |  |
| user_password | varchar(255) | YES |  | NULL |  |
| user_pass_salt | varchar(255) | YES |  | NULL |  |
| name | varchar(255) | YES |  | NULL |  |
| surname | varchar(255) | YES |  | NULL |  |
| email | varchar(255) | YES |  | NULL |  |
| phone | varchar(255) | YES |  | NULL |  |
| session_id | varchar(255) | YES |  | NULL |  |
| created_ip | varchar(255) | YES |  | NULL |  |
| created_dt | timestamp | YES |  | 'CURRENT_TIMESTAMP' | DEFAULT_GENERATED |
| last_logged_dt | timestamp | YES |  | NULL |  |
| disabled_dt | datetime | YES | MUL | NULL |  |
| status | int | YES |  | NULL |  |
| admin_lang | int | YES |  | NULL |  |

## Table: `admin_group`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| admin_group_id | int | NO | PRI | NULL | auto_increment |
| admin_group_name | varchar(255) | YES |  | NULL |  |
| pid | int | NO | MUL | NULL |  |
| code_name | varchar(255) | NO | MUL | NULL |  |

## Table: `admin_group_right`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| admin_group_right_id | int | NO | PRI | NULL | auto_increment |
| admin_group_id | int | YES | MUL | NULL |  |
| admin_right_id | int | YES | MUL | NULL |  |

## Table: `admin_in_group`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| admin_in_group_id | int | NO | PRI | NULL | auto_increment |
| admin_id | int | NO | MUL | NULL |  |
| group_id | int | NO | MUL | NULL |  |

## Table: `admin_presentation`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| admin_presentation_id | int | NO | PRI | NULL | auto_increment |
| admin_id | int | NO | MUL | NULL |  |
| presentation_id | int | NO | MUL | NULL |  |

## Table: `admin_right`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| admin_right_id | int | NO | PRI | NULL | auto_increment |
| name | varchar(255) | YES | MUL | NULL |  |
| right_code_name | varchar(255) | NO | MUL | NULL |  |
| group | varchar(255) | YES |  | NULL |  |

## Table: `cms_log`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id | int | NO | PRI | NULL | auto_increment |
| admin_id | int | NO |  | NULL |  |
| module | varchar(255) | NO |  | NULL |  |
| code_name | varchar(255) | YES |  | NULL |  |
| action | varchar(255) | NO |  | NULL |  |
| name | varchar(255) | NO |  | NULL |  |
| element_id | int | YES |  | NULL |  |
| component_id | int | YES |  | NULL |  |
| after | json | YES |  | NULL |  |
| before | json | YES |  | NULL |  |
| created_ip | varchar(255) | YES |  | NULL |  |
| created_dt | timestamp | YES |  | NULL |  |
| url | varchar(255) | YES |  | NULL |  |
| ssid | varchar(255) | YES |  | NULL |  |

## Table: `code_name`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id | int | NO | PRI | NULL | auto_increment |
| template_id | int | NO | MUL | NULL |  |
| module | int | NO | MUL | NULL |  |
| code_name | varchar(255) | NO | MUL | NULL |  |

## Table: `component`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| component_id | int | NO | PRI | NULL | auto_increment |
| component_name | varchar(255) | YES |  | NULL |  |
| module_id | int | NO | MUL | NULL |  |
| inserted | datetime | NO |  | NULL |  |
| code_name | varchar(255) | NO | MUL | NULL |  |

## Table: `config`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| config_id | int | NO | PRI | NULL | auto_increment |
| item | varchar(255) | NO |  | NULL |  |
| value | varchar(255) | NO |  | NULL |  |

## Table: `config_lang`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| config_lang_id | int | NO | PRI | NULL | auto_increment |
| config_id | int | NO | MUL | NULL |  |
| lang_id | int | NO | MUL | NULL |  |
| value | varchar(255) | NO |  | NULL |  |

## Table: `content_version`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| element_id | int | NO | PRI | NULL | auto_increment |
| content | text | YES |  | NULL |  |

## Table: `element`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| element_id | int | NO | PRI | NULL | auto_increment |
| component_id | int | NO | MUL | NULL |  |
| name | varchar(255) | NO |  | NULL |  |
| status_id | int | NO |  | NULL |  |
| author_id | int | NO |  | NULL |  |
| valid_from | date | YES |  | NULL |  |
| valid_to | date | YES |  | NULL |  |
| inserted | datetime | YES |  | NULL |  |

## Table: `file_manager`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| file_id | int | NO | PRI | NULL | auto_increment |
| element_id | int | YES |  | NULL |  |
| source_type | varchar(50) | YES | MUL | NULL |  |
| file_type | varchar(20) | YES |  | NULL |  |
| original_name | varchar(255) | NO |  | NULL |  |
| file_name | varchar(255) | NO |  | NULL |  |
| path | varchar(255) | NO |  | NULL |  |
| extension | varchar(10) | YES |  | NULL |  |
| mime_type | varchar(100) | YES |  | NULL |  |
| size | bigint | YES |  | NULL |  |
| created_dt | timestamp | YES |  | 'CURRENT_TIMESTAMP' | DEFAULT_GENERATED |
| admin_id | int | YES |  | NULL |  |

## Table: `install`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| install_id | int | NO | PRI | NULL | auto_increment |
| module_name | varchar(255) | NO | UNI | NULL |  |
| installed | tinyint(1) | NO |  | '0' |  |
| path | varchar(255) | NO |  | NULL |  |

## Table: `lookup`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| lookup_id | int | NO | PRI | NULL | auto_increment |
| parent_id | int | NO | MUL | NULL |  |
| item | varchar(255) | NO |  | NULL |  |
| constant | varchar(255) | YES |  | NULL |  |

## Table: `lookup_lang`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| lookup_lang_id | int | NO | PRI | NULL | auto_increment |
| lookup_id | int | NO | MUL | NULL |  |
| lang_id | int | NO |  | NULL |  |
| value | varchar(255) | YES |  | NULL |  |

## Table: `members`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| member_id | int | NO | PRI | NULL | auto_increment |
| member_number | int | YES |  | NULL |  |
| name | varchar(255) | YES |  | NULL |  |
| surname | varchar(255) | YES |  | NULL |  |
| degree | varchar(255) | YES |  | NULL |  |
| birth_date | date | YES |  | NULL |  |
| address | text | YES |  | NULL |  |
| email | varchar(255) | YES |  | NULL |  |
| phone | varchar(255) | YES |  | NULL |  |
| note | varchar(255) | YES |  | NULL |  |
| last_member_payment | date | YES |  | NULL |  |
| active | tinyint | YES |  | NULL |  |

## Table: `module`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| module_id | int | NO | PRI | NULL | auto_increment |
| install_id | int | NO |  | NULL |  |
| module_type | int | NO |  | NULL |  |
| module_active | enum('Y','N') | YES |  | 'N' |  |
| module_name | varchar(50) | YES |  | NULL |  |
| module_code_name | varchar(50) | NO | MUL | NULL |  |
| module_class_name | varchar(255) | NO | MUL | NULL |  |

## Table: `module_group_right`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| module_group_right_id | int | NO | PRI | NULL | auto_increment |
| admin_group_id | int | NO | MUL | NULL |  |
| module_id | int | NO | MUL | NULL |  |
| permission_id | int | NO | MUL | NULL |  |

## Table: `module_permission`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| permission_id | int | NO | PRI | NULL | auto_increment |
| name | varchar(50) | NO |  | NULL |  |
| right_code_name | varchar(50) | NO | MUL | NULL |  |

## Table: `module_right`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| module_right_id | int | NO | PRI | NULL | auto_increment |
| module_id | int | NO | MUL | NULL |  |
| permission_id | int | NO |  | NULL |  |

## Table: `news`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| element_id | int | YES |  | NULL |  |
| title | varchar(255) | YES |  | NULL |  |
| short_text | varchar(255) | YES |  | NULL |  |
| content | longtext | YES |  | NULL |  |
| image | varchar(255) | YES |  | NULL |  |

## Table: `page`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| page_id | int | NO | PRI | NULL | auto_increment |
| page_parent_id | int | NO | MUL | '0' |  |
| presentation_id | int | NO |  | NULL |  |
| page_status | int | NO | MUL | NULL |  |
| position | int | YES |  | NULL |  |
| template_id | int | NO | MUL | NULL |  |
| page_name | varchar(255) | NO |  | NULL |  |
| page_description | varchar(255) | YES |  | NULL |  |
| page_keywords | varchar(255) | YES |  | NULL |  |
| page_title | varchar(255) | YES |  | NULL |  |
| page_rewrite | varchar(255) | YES |  | NULL |  |
| page_redirect | varchar(255) | YES |  | NULL |  |
| page_redirect_id | int | NO |  | NULL |  |
| page_sitemap | enum('Y','N') | YES |  | 'N' |  |
| page_menu | enum('Y','N') | YES |  | 'N' |  |
| restricted_area | enum('Y','N') | NO |  | 'N' |  |

## Table: `page_component`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| page_component_id | int | NO | PRI | NULL | auto_increment |
| page_id | int | NO | MUL | NULL |  |
| component_id | int | YES | MUL | NULL |  |

## Table: `page_component_action`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| element_id | int | NO | PRI | NULL | auto_increment |
| page_id | int | NO | MUL | NULL |  |
| module | varchar(255) | NO |  | NULL |  |
| component_id | int | NO | MUL | '0' |  |
| action | varchar(255) | NO |  | NULL |  |
| param | varchar(255) | YES |  | NULL |  |

## Table: `page_group`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id | int | NO | PRI | NULL | auto_increment |
| name | varchar(255) | NO |  | NULL |  |

## Table: `page_group_admin_group`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| page_group_admin_group_id | int | NO | PRI | NULL | auto_increment |
| page_group_id | int | NO | MUL | NULL |  |
| admin_group_id | int | NO |  | NULL |  |

## Table: `page_in_group`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| page_in_group_id | int | NO | PRI | NULL | auto_increment |
| page_group_id | int | NO | MUL | NULL |  |
| page_id | int | NO | MUL | NULL |  |

## Table: `page_in_group_user`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| page_in_group_user_id | int | NO | PRI | NULL | auto_increment |
| page_group_id | int | NO | MUL | NULL |  |
| page_id | int | NO | MUL | NULL |  |

## Table: `presentation`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| presentation_id | int | NO | PRI | NULL | auto_increment |
| presentation_lang | int | NO | MUL | NULL |  |
| presentation_status | int | NO | MUL | NULL |  |
| presentation_name | varchar(255) | YES |  | NULL |  |
| domain | varchar(255) | YES | MUL | NULL |  |
| directory | varchar(50) | NO |  | NULL |  |
| presentation_description | varchar(255) | YES |  | NULL |  |
| presentation_keywords | varchar(255) | YES |  | NULL |  |
| is_default | int | NO |  | '0' |  |

## Table: `presentation_component_action`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| element_id | int | NO | PRI | NULL | auto_increment |
| presentation_id | int | NO | MUL | NULL |  |
| component_id | int | YES |  | NULL |  |
| module | varchar(255) | NO |  | NULL |  |
| action | varchar(255) | NO |  | NULL |  |
| params | text | YES |  | NULL |  |

## Table: `spec_param_page`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| spec_param_id | int | NO | PRI | NULL | auto_increment |
| page_id | int | NO | MUL | NULL |  |
| name | varchar(255) | NO |  | NULL |  |
| value | varchar(255) | NO |  | NULL |  |

## Table: `spec_param_presentation`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| spec_param_id | int | NO | PRI | NULL | auto_increment |
| presentation_id | int | NO | MUL | NULL |  |
| name | varchar(255) | NO |  | NULL |  |
| value | varchar(255) | NO |  | NULL |  |

## Table: `template`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| template_id | int | NO | PRI | NULL | auto_increment |
| template_type | int | YES |  | NULL |  |
| template_filename | varchar(255) | YES |  | NULL |  |
| template_name | varchar(255) | NO |  | NULL |  |
| template_path | varchar(255) | YES |  | NULL |  |
| presentation_id | int | NO |  | NULL |  |

## Table: `version`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| component_id | int | NO | MUL | NULL |  |
| element_id | int | NO |  | NULL |  |

## Table: `web_text`

| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| web_text_id | int | NO | PRI | NULL | auto_increment |
| code | varchar(255) | YES |  | NULL |  |
| text | longtext | YES |  | NULL |  |

