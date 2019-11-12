<?php // 2019-11-10T11.26.39Z_AddGlobalPermissions.php

namespace Breyta\Migration;

use Breyta\AbstractMigration;

class AddGlobalPermissions extends AbstractMigration
{
    public function up(): void
    {
        $this->exec("CREATE TABLE permissions (
            key CHARACTER VARYING(128) NOT NULL,
            description TEXT,
            category CHARACTER VARYING(25) DEFAULT 'ultimate' NOT NULL,
            created TIMESTAMP DEFAULT now() NOT NULL,
            updated TIMESTAMP DEFAULT now() NOT NULL,
            CONSTRAINT permissions_pkey PRIMARY KEY (key)
        )");

        $this->exec("CREATE TABLE user_permissions (
            user_id INTEGER NOT NULL,
            permission_key VARCHAR(128) NOT NULL,
            restrict BOOLEAN DEFAULT FALSE NOT NULL,
            added TIMESTAMP DEFAULT now() NOT NULL,
            CONSTRAINT user_permissions_pkey PRIMARY KEY (user_id, permission_key),
            CONSTRAINT user_permissions_user_fkey FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE,
            CONSTRAINT user_permissions_permission_fkey FOREIGN KEY (permission_key) REFERENCES permissions (key)
                ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE roles (
            id SERIAL,
            name CHARACTER VARYING(50) NOT NULL,
            description TEXT,
            created TIMESTAMP DEFAULT now() NOT NULL,
            updated TIMESTAMP DEFAULT now() NOT NULL,
            CONSTRAINT roles_pkey PRIMARY KEY (id),
            CONSTRAINT roles_name_uindex UNIQUE (name)
        )");

        $this->exec("CREATE TABLE role_permissions (
            role_id INTEGER NOT NULL,
            permission_key VARCHAR(128) NOT NULL,
            restrict BOOLEAN DEFAULT FALSE NOT NULL,
            added TIMESTAMP DEFAULT now() NOT NULL,
            CONSTRAINT role_permissions_pkey PRIMARY KEY (role_id, permission_key),
            CONSTRAINT role_permissions_role_fkey FOREIGN KEY (role_id) REFERENCES roles (id)
                ON DELETE CASCADE,
            CONSTRAINT role_permissions_permission_fkey FOREIGN KEY (permission_key) REFERENCES permissions (key)
                ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE user_roles (
            user_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            added TIMESTAMP DEFAULT now() NOT NULL,
            CONSTRAINT user_roles_pkey PRIMARY KEY (user_id, role_id),
            CONSTRAINT user_roles_user_fkey FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE,
            CONSTRAINT user_roles_role_fkey FOREIGN KEY (role_id) REFERENCES roles (id)
                ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE role_inherits (
            child_id INTEGER NOT NULL,
            parent_id INTEGER NOT NULL,
            added TIMESTAMP DEFAULT now() NOT NULL,
            CONSTRAINT role_inherits_pkey PRIMARY KEY (child_id, parent_id),
            CONSTRAINT role_inherits_child_fkey FOREIGN KEY (child_id) REFERENCES roles (id)
                ON DELETE CASCADE,
            CONSTRAINT role_inherits_parent_fkey FOREIGN KEY (parent_id) REFERENCES roles (id)
                ON DELETE CASCADE
        )");
    }

    public function down(): void
    {
        $this->exec("DROP TABLE role_inherits");
        $this->exec("DROP TABLE user_roles");
        $this->exec("DROP TABLE role_permissions");
        $this->exec("DROP TABLE user_permissions");
        $this->exec("DROP TABLE permissions");
        $this->exec("DROP TABLE roles");
    }
}
