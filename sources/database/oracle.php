<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_database_drivers
 */

/*EXTRA FUNCTIONS: oci.+*/

/*
For: php_oci8.dll
*/

/**
 * Database Driver.
 *
 * @package    core_database_drivers
 */
class Database_Static_oracle
{
    public $cache_db = array();

    /**
     * Get the default user for making db connections (used by the installer as a default).
     *
     * @return string The default user for db connections
     */
    public function db_default_user()
    {
        return 'system';
    }

    /**
     * Get the default password for making db connections (used by the installer as a default).
     *
     * @return string The default password for db connections
     */
    public function db_default_password()
    {
        return '';
    }

    /**
     * Get SQL for creating a table index.
     *
     * @param  ID_TEXT $table_name The name of the table to create the index on
     * @param  ID_TEXT $index_name The index name (not really important at all)
     * @param  string $_fields Part of the SQL query: a comma-separated list of fields to use on the index
     * @param  array $db The DB connection to make on
     * @param  ID_TEXT $raw_table_name The table name with no table prefix
     * @param  string $unique_key_fields The name of the unique key field for the table
     * @return array List of SQL queries to run
     */
    public function db_create_index($table_name, $index_name, $_fields, $db, $raw_table_name, $unique_key_fields)
    {
        if ($index_name[0] == '#') {
            $ret = array();
            $index_name = substr($index_name, 1);
            $fields = explode(',', $_fields);
            foreach ($fields as $field) {
                $ret[] = 'CREATE INDEX ' . $index_name . ' ON ' . $table_name . '(' . $field . ') INDEXTYPE IS CTXSYS.CONTEXT PARAMETERS(\'lexer theme_lexer\')';
                $ret[] = 'EXEC DBMS_STATS.GATHER_TABLE_STATS(USER,\'' . $table_name . '\',cascade=>TRUE)';
            }
            return $ret;
        }

        $_fields = preg_replace('#\(\d+\)#', '', $_fields);

        $fields = explode(',', $_fields);
        foreach ($fields as $field) {
            $db_type = $GLOBALS['SITE_DB']->query_select_value_if_there('db_meta', 'm_type', array('m_table' => $raw_table_name, 'm_name' => $field));
            if ((strpos($db_type, 'LONG') !== false) || ((!multi_lang_content()) && (strpos($db_type, 'SHORT_TRANS') !== false))) {
                // We can't support this in SQL Server http://www.oratable.com/ora-01450-maximum-key-length-exceeded/.
                // We assume shorter numbers than 250 are only being used on short columns anyway, which will index perfectly fine without any constraint.
                return array();
            }
        }

        return array('CREATE INDEX ' . $index_name . '__' . $table_name . ' ON ' . $table_name . '(' . $_fields . ')');
    }

    /**
     * Change the primary key of a table.
     *
     * @param  ID_TEXT $table_name The name of the table to create the index on
     * @param  array $new_key A list of fields to put in the new key
     * @param  array $db The DB connection to make on
     */
    public function db_change_primary_key($table_name, $new_key, $db)
    {
        $this->db_query('ALTER TABLE ' . $table_name . ' DROP PRIMARY KEY', $db);
        $this->db_query('ALTER TABLE ' . $table_name . ' ADD PRIMARY KEY (' . implode(',', $new_key) . ')', $db);
    }

    /**
     * Assemble part of a WHERE clause for doing full-text search
     *
     * @param  string $content Our match string (assumes "?" has been stripped already)
     * @param  boolean $boolean Whether to do a boolean full text search
     * @return string Part of a WHERE clause for doing full-text search
     */
    public function db_full_text_assemble($content, $boolean)
    {
        $content = str_replace('"', '', $content);
        return 'CONTAINS ((?),\'' . $this->db_escape_string($content) . '\')';
    }

    /**
     * Get the ID of the first row in an auto-increment table (used whenever we need to reference the first).
     *
     * @return integer First ID used
     */
    public function db_get_first_id()
    {
        return 1;
    }

    /**
     * Get a map of Composr field types, to actual database types.
     *
     * @return array The map
     */
    public function db_get_type_remap()
    {
        $type_remap = array(
            'AUTO' => 'integer',
            'AUTO_LINK' => 'integer',
            'INTEGER' => 'integer',
            'UINTEGER' => 'bigint',
            'SHORT_INTEGER' => 'smallint',
            'REAL' => 'real',
            'BINARY' => 'smallint',
            'MEMBER' => 'integer',
            'GROUP' => 'integer',
            'TIME' => 'bigint',
            'LONG_TRANS' => 'bigint',
            'SHORT_TRANS' => 'bigint',
            'LONG_TRANS__COMCODE' => 'integer',
            'SHORT_TRANS__COMCODE' => 'integer',
            'SHORT_TEXT' => 'text',
            'LONG_TEXT' => 'CLOB',
            'ID_TEXT' => 'varchar(80)',
            'MINIID_TEXT' => 'varchar(40)',
            'IP' => 'varchar(40)',
            'LANGUAGE_NAME' => 'varchar(5)',
            'URLPATH' => 'varchar(255)',
        );
        return $type_remap;
    }

    /**
     * Get SQL for creating a new table.
     *
     * @param  ID_TEXT $table_name The table name
     * @param  array $fields A map of field names to Composr field types (with *#? encodings)
     * @param  array $db The DB connection to make on
     * @param  ID_TEXT $raw_table_name The table name with no table prefix
     * @param  boolean $save_bytes Whether to use lower-byte table storage, with tradeoffs of not being able to support all unicode characters; use this if key length is an issue
     * @return array List of SQL queries to run
     */
    public function db_create_table($table_name, $fields, $db, $raw_table_name, $save_bytes = false)
    {
        $type_remap = $this->db_get_type_remap();

        $_fields = '';
        $keys = '';
        $trigger = false;
        foreach ($fields as $name => $type) {
            if ($type[0] == '*') { // Is a key
                $type = substr($type, 1);
                if ($keys != '') {
                    $keys .= ', ';
                }
                $keys .= $name;
            }

            if ($type[0] == '?') { // Is perhaps null
                $type = substr($type, 1);
                $perhaps_null = 'NULL';
            } else {
                $perhaps_null = 'NOT NULL';
            }

            if ($type == 'AUTO') {
                $trigger = true;
            }

            $type = isset($type_remap[$type]) ? $type_remap[$type] : $type;

            $_fields .= '    ' . $name . ' ' . $type;
            if (substr($name, -13) == '__text_parsed') {
                $_fields .= ' DEFAULT \'\'';
            } elseif (substr($name, -13) == '__source_user') {
                $_fields .= ' DEFAULT ' . strval(db_get_first_id());
            }
            $_fields .= ' ' . $perhaps_null . ',' . "\n";
        }

        $ret = array();

        $ret[] = 'CREATE TABLE ' . $table_name . ' (' . "\n" . $_fields . '    PRIMARY KEY (' . $keys . ")\n)";

        if ($trigger) {
            $ret[] = "CREATE SEQUENCE gen_$table_name";
            $ret[] = "CREATE OR REPLACE TRIGGER gen_$table_name BEFORE INSERT ON $table_name
                FOR EACH ROW
                BEGIN
                    SELECT gen_$table_name.nextval
                    into :new.id
                    from dual;
                END";
        }

        return $ret;
    }

    /**
     * Encode an SQL statement fragment for a conditional to see if two strings are equal.
     *
     * @param  ID_TEXT $attribute The attribute
     * @param  string $compare The comparison
     * @return string The SQL
     */
    public function db_string_equal_to($attribute, $compare)
    {
        return $attribute . " LIKE '" . $this->db_escape_string($compare) . "'";
    }

    /**
     * Encode an SQL statement fragment for a conditional to see if two strings are not equal.
     *
     * @param  ID_TEXT $attribute The attribute
     * @param  string $compare The comparison
     * @return string The SQL
     */
    public function db_string_not_equal_to($attribute, $compare)
    {
        return $attribute . "<>'" . $this->db_escape_string($compare) . "'";
    }

    /**
     * This function is internal to the database system, allowing SQL statements to be build up appropriately. Some databases require IS NULL to be used to check for blank strings.
     *
     * @return boolean Whether a blank string IS NULL
     */
    public function db_empty_is_null()
    {
        return true;
    }

    /**
     * Find whether table truncation support is present
     *
     * @return boolean Whether it is
     */
    public function db_supports_truncate_table()
    {
        return true;
    }

    /**
     * Delete a table.
     *
     * @param  ID_TEXT $table The table name
     * @param  array $db The DB connection to delete on
     * @return array List of SQL queries to run
     */
    public function db_drop_table_if_exists($table, $db)
    {
        return array('DROP TABLE ' . $table);
    }

    /**
     * Determine whether the database is a flat file database, and thus not have a meaningful connect username and password.
     *
     * @return boolean Whether the database is a flat file database
     */
    public function db_is_flat_file_simple()
    {
        return false;
    }

    /**
     * Encode a LIKE string comparision fragement for the database system. The pattern is a mixture of characters and ? and % wildcard symbols.
     *
     * @param  string $pattern The pattern
     * @return string The encoded pattern
     */
    public function db_encode_like($pattern)
    {
        return $this->db_escape_string($pattern);
    }

    /**
     * Close the database connections. We don't really need to close them (will close at exit), just disassociate so we can refresh them.
     */
    public function db_close_connections()
    {
        foreach ($this->cache_db as $db) {
            foreach ($db as $_db) {
                ocicommit($_db);
            }
        }
    }

    /**
     * Get a database connection. This function shouldn't be used by you, as a connection to the database is established automatically.
     *
     * @param  boolean $persistent Whether to create a persistent connection
     * @param  string $db_name The database name
     * @param  string $db_host The database host (the server)
     * @param  string $db_user The database connection username
     * @param  string $db_password The database connection password
     * @param  boolean $fail_ok Whether to on error echo an error and return with a null, rather than giving a critical error
     * @return ?array A database connection (null: failed)
     */
    public function db_get_connection($persistent, $db_name, $db_host, $db_user, $db_password, $fail_ok = false)
    {
        if ($db_host != 'localhost') {
            fatal_exit(do_lang_tempcode('ONLY_LOCAL_HOST_FOR_TYPE'));
        }

        // Potential caching
        if (isset($this->cache_db[$db_name][$db_host])) {
            return $this->cache_db[$db_name][$db_host];
        }

        if (!function_exists('ocilogon')) {
            $error = 'The oracle PHP extension not installed (anymore?). You need to contact the system administrator of this server.';
            if ($fail_ok) {
                echo ((running_script('install')) && (get_param_string('type', '') == 'ajax_db_details')) ? strip_html($error) : $error;
                return null;
            }
            critical_error('PASSON', $error);
        }

        $db = $persistent ? @ociplogon($db_user, $db_password, $db_name) : @ocilogon($db_user, $db_password, $db_name);
        if ($db === false) {
            $error = 'Could not connect to database-server (' . ocierror() . ')';
            if ($fail_ok) {
                echo ((running_script('install')) && (get_param_string('type', '') == 'ajax_db_details')) ? strip_html($error) : $error;
                return null;
            }
            critical_error('PASSON', $error); //warn_exit(do_lang_tempcode('CONNECT_DB_ERROR'));
        }

        if ($db === false) {
            fatal_exit(do_lang('CONNECT_DB_ERROR'));
        }
        $this->cache_db[$db_name][$db_host] = $db;
        return $db;
    }

    /**
     * Get the number of rows in a table, with approximation support for performance (if necessary on the particular database backend).
     *
     * @param string $table The table name
     * @param array $where WHERE clauses if it will help get a more reliable number when we're not approximating in map form
     * @param string $where_clause WHERE clauses if it will help get a more reliable number when we're not approximating in SQL form
     * @param object $db The DB connection to check against
     * @return ?integer The count (null: do it normally)
     */
    public function get_table_count_approx($table, $where, $where_clause, $db)
    {
        $sql = 'SELECT NUM_ROWS FROM ALL_TABLES WHERE TABLE_NAME=\'' . strtoupper($db->get_table_prefix() . $table) . '\'';
        return $db->query_value_if_there($sql, false, true);
    }

    /**
     * Find whether full-text-search is present
     *
     * @param  array $db A DB connection
     * @return boolean Whether it is
     */
    public function db_has_full_text($db)
    {
        return false;
    }

    /**
     * Find whether full-text-boolean-search is present
     *
     * @return boolean Whether it is
     */
    public function db_has_full_text_boolean()
    {
        return false;
    }

    /**
     * Escape a string so it may be inserted into a query. If SQL statements are being built up and passed using db_query then it is essential that this is used for security reasons. Otherwise, the abstraction layer deals with the situation.
     *
     * @param  string $string The string
     * @return string The escaped string
     */
    public function db_escape_string($string)
    {
        $string = fix_bad_unicode($string);

        $string = str_replace("'", "''", $string);
        return str_replace('&', '\&', $string);
    }

    /**
     * Adjust an SQL query to apply offset/limit restriction.
     *
     * @param  string $query The complete SQL query
     * @param  ?integer $max The maximum number of rows to affect (null: no limit)
     * @param  ?integer $start The start row to affect (null: no specification)
     */
    public function apply_sql_limit_clause(&$query, $max = null, $start = 0)
    {
        if (($start !== null) && ($max !== null) && (strtoupper(substr(ltrim($query), 0, 7)) == 'SELECT ') || (strtoupper(substr(ltrim($query), 0, 8)) == '(SELECT ')) {
            $old_query = $query;

            if (is_null($start)) {
                $start = 0;
            }

            $pos = stripos($old_query, 'FROM ');
            $pos2 = strpos($old_query, ' ', $pos + 5);
            $pos3 = stripos($old_query, 'WHERE ', $pos2);
            if ($pos3 === false) { // No where
                $pos4 = stripos($old_query, ' ORDER BY');
                if ($pos4 === false) {
                    $pos4 = strlen($old_query);
                }
                $query = substr($old_query, 0, $pos4) . ' WHERE rownum>=' . strval(intval($start));
                if (!is_null($max)) {
                    $query .= ' AND rownum<' . strval(intval($start + $max));
                }
                $query .= substr($old_query, $pos4);
            } else {
                $pos4 = stripos($old_query, ' ORDER BY');
                if ($pos4 === false) {
                    $pos4 = strlen($old_query);
                }
                $query = substr($old_query, 0, $pos3) . 'WHERE (' . substr($old_query, $pos3 + 6, $pos4 - $pos3 - 6) . ') AND rownum>=' . strval(intval($start));
                if (!is_null($max)) {
                    $query .= ' AND rownum<' . strval(intval($start + $max));
                }
                $query .= substr($old_query, $pos4);
            }
        }
    }

    /**
     * This function is a very basic query executor. It shouldn't usually be used by you, as there are abstracted versions available.
     *
     * @param  string $query The complete SQL query
     * @param  array $db A DB connection
     * @param  ?integer $max The maximum number of rows to affect (null: no limit)
     * @param  ?integer $start The start row to affect (null: no specification)
     * @param  boolean $fail_ok Whether to output an error on failure
     * @param  boolean $get_insert_id Whether to get the autoincrement ID created for an insert query
     * @return ?mixed The results (null: no results), or the insert ID
     */
    public function db_query($query, $db, $max = null, $start = null, $fail_ok = false, $get_insert_id = false)
    {
        $this->apply_sql_limit_clause($query, $max, $start);

        $stmt = ociparse($db, $query);
        $results = @ociexecute($stmt);
        if ((($results === false) || (((strtoupper(substr(ltrim($query), 0, 7)) == 'SELECT ') || (strtoupper(substr(ltrim($query), 0, 8)) == '(SELECT ')) && ($results === true))) && (!$fail_ok)) {
            $err = ocierror($db);
            if (function_exists('ocp_mark_as_escaped')) {
                ocp_mark_as_escaped($err);
            }
            if ((!running_script('upgrader')) && ((!get_mass_import_mode()) || (get_param_integer('keep_fatalistic', 0) == 1))) {
                if (!function_exists('do_lang') || is_null(do_lang('QUERY_FAILED', null, null, null, null, false))) {
                    fatal_exit(htmlentities('Query failed: ' . $query . ' : ' . $err));
                }

                fatal_exit(do_lang_tempcode('QUERY_FAILED', escape_html($query), ($err)));
            } else {
                echo htmlentities('Database query failed: ' . $query . ' [') . ($err) . htmlentities(']') . "<br />\n";
                return null;
            }
        }

        $sub = substr(ltrim($query), 0, 4);
        if (($results !== true) && (($sub === '(SEL') || ($sub === 'SELE') || ($sub === 'sele') || ($sub === 'CHEC') || ($sub === 'EXPL') || ($sub === 'REPA') || ($sub === 'DESC') || ($sub === 'SHOW')) && ($results !== false)) {
            return $this->db_get_query_rows($stmt, $query, $start);
        }

        if ($get_insert_id) {
            if (strtoupper(substr($query, 0, 7)) == 'UPDATE ') {
                return null;
            }

            $pos = strpos($query, '(');
            $table_name = substr($query, 12, $pos - 13);

            $stmt = ociparse($db, 'SELECT gen_' . $table_name . '.CURRVAL AS v FROM dual');
            ociexecute($stmt);
            $ar2 = ocifetch($stmt);
            return $ar2[0];
        }

        return null;
    }

    /**
     * Get the rows returned from a SELECT query.
     *
     * @param  resource $stmt The query result pointer
     * @param  string $query The complete SQL query (useful for debugging)
     * @param  ?integer $start Whether to start reading from (null: irrelevant)
     * @return array A list of row maps
     */
    public function db_get_query_rows($stmt, $query, $start = null)
    {
        $out = array();
        $i = 0;

        $num_fields = ocinumcols($stmt);
        $types = array();
        $names = array();
        for ($x = 1; $x <= $num_fields; $x++) {
            $types[$x] = ocicolumntype($stmt, $x);
            $names[$x] = strtolower(ocicolumnname($stmt, $x));
        }
        while (ocifetch($stmt)) {
            if ((is_null($start)) || ($i >= $start)) {
                $newrow = array();

                for ($j = 1; $j <= $num_fields; $j++) {
                    $v = ociresult($stmt, $j);
                    if (is_object($v)) {
                        $v = $v->load(); // For CLOB's
                    }
                    if ($v === false) {
                        fatal_exit(do_lang_tempcode('QUERY_FAILED', ocierror($stmt)));
                    }

                    $name = $names[$j];
                    $type = $types[$j];

                    if ($type == 'NUMBER') {
                        if (!is_null($v)) {
                            $newrow[$name] = intval($v);
                        } else {
                            $newrow[$name] = null;
                        }
                    } elseif ((substr($type, 0, 5) == 'FLOAT') || substr($type, 0, 6) == 'NUMBER') {
                        $newrow[$name] = floatval($v);
                    } else {
                        if ($v == ' ') {
                            $v = '';
                        }
                        $newrow[$name] = $v;
                    }
                }

                $out[] = $newrow;
            }

            $i++;
        }

        return $out;
    }
}
