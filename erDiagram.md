erDiagram

    clients {
        bigint id PK
        string first_name
        string last_name
        string email UK "nullable"
        string phone_number "nullable"
        string address "nullable"
        string city "nullable"
        string state "nullable"
        string zip_code "nullable"
        string apt "nullable"
        timestamp created_at
        timestamp updated_at
    }

    project_statuses {
        bigint id PK
        string display_name "Borrador, En Ejecución, Finalizado, Cancelado"
        string code UK "unique (draft, in_progress, completed, cancelled)"
        string icon
        string bg_color
        string bg_text
        timestamp created_at
        timestamp updated_at
    }

    projects {
        bigint id PK
        bigint client_id FK "clients"
        bigint project_status_id FK "project_statuses"
        string code "unique"
        string title "Ej: Pintura Residencia Alfa"
        string address "nullable"
        date actual_start_date "nullable"
        date actual_end_date "nullable"
        text description 
        timestamp created_at
        timestamp updated_at
    }


    quote_statuses {
        bigint id PK
        string display_name "Borrador, Aprobada, Cerrada por Enmienda, Cancelada"
        string code UK "unique (draft, approved, closed_by_amendment, cancelled)"
        string icon
        string bg_color
        string bg_text
        timestamp created_at
        timestamp updated_at
    }     

    quotes {
        bigint id PK
        bigint project_id FK "projects"
        bigint quote_status_id FK "quote_statuses"
        bigint parent_quote_id FK "quotes.id (nullable para Enmiendas)"
        string title 
        date start_date "Fecha estimada de inicio"
        date end_date "Fecha estimada de fin"
        boolean work_weekends "DEFAULT: false."
        int amendment_level "DEFAULT: 0. Original = 0, Enmienda 1 = 1, Enmienda 2 = 2"
        int total_hours "Suma total de horas de mano de obra"
        decimal direct_labor_cost "Suma de quote_labor_items.subtotal"
        decimal direct_materials_cost "Suma de quote_material_items.subtotal"
        decimal direct_cost "Costo directo total (Mano de Obra + Materiales)"
        decimal overhead_rate_applied "SNAPSHOT: T_oh al momento de aprobar"
        decimal overtime_multiplier_applied "SNAPSHOT: multiplicador de extras al aprobar"
        decimal overhead_cost "SNAPSHOT: total_hours * overhead_rate_applied"
        decimal equilibrium_cost "SNAPSHOT: direct_cost + overhead_cost"
        decimal margin_applied "Margen de ganancia pactado (ej: 30.00)"
        decimal total_price "SNAPSHOT: Precio final facturado al cliente"
        timestamp created_at
        timestamp updated_at
    }

    quote_labor_assignments {
        bigint id PK
        bigint quote_id FK "quotes"
        bigint labor_role_id FK "labor_roles"
        bigint employee_id FK "employees (nullable para estimación)"
        string worker_name_placeholder "nullable (Ej: Pintor 1)"
        int estimated_hours_regular
        int estimated_hours_extra
        decimal hourly_rate_at_estimation "Snapshot de C_ch"
        decimal estimated_subtotal
        timestamp created_at
        timestamp updated_at
    }

    quote_material_items {
        bigint id PK
        bigint quote_id FK "quotes"
        string concept "Ej: Pintura Benjamin Moore 5G"
        decimal estimated_quantity "Cantidad estimada"
        decimal estimated_unit_price "Costo unitario estimado"
        decimal subtotal "SNAPSHOT: quantity * unit_price"
        timestamp created_at
        timestamp updated_at
    }

    employees {
        bigint id PK
        string name UK "Ej: Alex, Johan Lince, Carlos"
        boolean is_active "Default: true"
        timestamp created_at
        timestamp updated_at
    }

    users {
        bigint id PK
        string name
        string email UK
        string password
        timestamp created_at
        timestamp updated_at
    }

    fixed_expenses {
        bigint id PK
        string concept "Ej: Renta de Oficina, Seguros de Carro"
        decimal amount "Monto mensual"
        boolean is_active "Default: true"
        timestamp created_at
        timestamp updated_at
    }

    global_settings {
        bigint id PK
        decimal standard_monthly_hours "Capacidad estándar mensual"
        decimal default_overhead_rate_applied "T_oh global calculada"
        decimal default_profit_margin "Margen por defecto"
        decimal overtime_multiplier "Multiplicador hora extra (ej: 1.5)"
        timestamp created_at
        timestamp updated_at
    }

    labor_roles {
        bigint id PK
        string name UK
        decimal base_salary
        decimal social_load_pct
        decimal hourly_cost "C_ch calculada y persistida"
        boolean is_active "Default: true"
        timestamp created_at
        timestamp updated_at
    }  

    project_labor_logs {
        bigint id PK
        bigint project_id FK "projects"
        bigint quote_labor_assignment_id FK "quote_labor_assignments (nullable)"
        bigint employee_id FK "employees"
        bigint labor_role_id FK "labor_roles"
        bigint annulled_by_user_id FK "users (nullable)"
        int actual_hours_regular
        int actual_hours_extra
        decimal hourly_rate_actual "Costo real de pago"
        decimal overtime_multiplier_applied "SNAPSHOT: Multiplicador al registrar"
        decimal actual_subtotal "Calculado por backend"
        date logged_at
        boolean is_annulled "DEFAULT: false"
        timestamp annulled_at "nullable"
        string annulment_reason "nullable"
        timestamp created_at
        timestamp updated_at
    }

    material_categories{
        bigint id PK
        string display_name "budgeted, unbudgeted"
        string code UK
        timestamp created_at
        timestamp updated_at
    }

    project_material_purchases {
        bigint id PK
        bigint project_id FK "projects"
        bigint material_category_id FK "material_categories"
        bigint quote_material_item_id FK "quote_material_items (nullable)"
        bigint annulled_by_user_id FK "users (nullable)"
        string concept "Ej: Ben 5 Galon"
        string store "Ej: Sherwin-Williams"
        string payment_method "Cash, Check, Credit Card, Zelle"
        string buyer_name "Persona que compró"
        decimal actual_quantity
        decimal actual_unit_price
        decimal actual_subtotal "quantity * unit_price"  
        date purchased_at
        boolean is_annulled "DEFAULT: false"
        timestamp annulled_at "nullable"
        string annulment_reason "nullable"
        timestamp created_at
        timestamp updated_at
    }

    project_deposits {
        bigint id PK
        bigint project_id FK "projects"
        bigint annulled_by_user_id FK "users (nullable)"
        decimal amount "Monto del anticipo/pago"
        string payment_method "Cash, Check, Credit Card, Zelle"
        date received_at
        string reference_number "nullable"
        boolean is_annulled "DEFAULT: false"
        timestamp annulled_at "nullable"
        string annulment_reason "nullable"
        timestamp created_at
        timestamp updated_at
    }
