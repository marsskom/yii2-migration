<?php

namespace studio32x32\components\migration;

use InvalidArgumentException;
use RuntimeException;
use Yii;

/**
 * Extended Yii2 migration class for representing a database migration.
 *
 * @category component
 * @package studio32x32\components\migration
 * @author Andriy Prakapas <marsskom@gmail.com>
 * @copyright 2017 32x32.com.ua
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0
 * @link https://github.com/studio32x32/yii2-migration
 */
class Migration extends \yii\db\Migration
{
    /**
     * Additional table options
     * @var string
     */
    protected $tableOptions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        switch (Yii::$app->db->driverName) {
            case 'mysql':
                $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
                break;
            case 'pgsql':
                $this->tableOptions = null;
                break;
            default:
                throw new RuntimeException('Your database is not supported!');
        }
    }

    /**
     * Return real table name from Yii2 table name template
     * @param  string $table
     * @return string
     */
    public function tableName($table)
    {
        $table = str_replace(['{{', '}}'], '', $table);
        $table = str_replace('%', Yii::$app->db->tablePrefix, $table);
        return $table;
    }

    /**
     * Check if there is a table in the database
     * @param  string $tableName
     * @return boolean
     */
    public function tableExists($tableName)
    {
        if (!is_string($tableName) || empty($tableName)) {
            throw new InvalidArgumentException('Table name must be non empty string.');
        }

        $tableName = $this->tableName($tableName);
        return in_array($tableName, Yii::$app->db->schema->tableNames);
    }

    /**
     * Create `TINYINT` field
     * @param  integer $length
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized
     * @see http://dev.mysql.com/doc/refman/5.7/en/integer-types.html
     */
    public function tinyint($length = 1)
    {
        if (!is_int($length)) {
            $length = 1;
        }

        return $this->getDb()->getSchema()->createColumnSchemaBuilder('TINYINT', $length);
    }

    /**
     * Create `ENUM` field
     * @param  array $values enum values
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized
     * @see http://dev.mysql.com/doc/refman/5.7/en/enum.html
     */
    public function enum($values)
    {
        if (!is_array($values) || empty($values)) {
            throw new InvalidArgumentException('Enum values is invalid');
        }

        // Build ENUM
        $query = 'ENUM("' . implode('", "', $values) . '")';
        return $this->getDb()->getSchema()->createColumnSchemaBuilder($query);
    }

    /**
     * Create `AFTER` MySQL syntax
     * @param  string $column after column name
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized
     * @throws InvalidArgumentException
     * @see http://dev.mysql.com/doc/refman/5.7/en/alter-table.html
     */
    public function after($column)
    {
        if (!is_string($column)) {
            throw new InvalidArgumentException("After column name is not string");
        }

        return $this->getDb()->getSchema()->createColumnSchemaBuilder('AFTER `' . $column . '`');
    }

    /**
     * Create `COMMENT` MySQL syntax
     * @param $comment
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized
     * @throws  InvalidArgumentException
     * @see http://dev.mysql.com/doc/refman/5.7/en/create-table.html
     */
    public function comment($comment)
    {
        if (!is_string($comment) || empty($comment)) {
            throw new InvalidArgumentException("Comment must be not empty string");
        }

        return $this->getDb()->getSchema()->createColumnSchemaBuilder('COMMENT "' . $comment . '"');
    }

    /**
     * Create `BLOB` column
     * @param string $type MySQL blob type: TINYBLOB, BLOB, MEDIUMBLOB, LONGBLOB, **default** - BLOB
     * @return \yii\db\ColumnSchemaBuilder
     * @see http://dev.mysql.com/doc/refman/5.7/en/blob.html
     */
    public function blob($type = 'BLOB')
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder($type);
    }

    /**
     * Create `TEXT` column
     * @param string $type MySQL text type: TINYTEXT, TEXT, MEDIUMTEXT, LONGTEXT, **default** - TEXT
     * @return \yii\db\ColumnSchemaBuilder
     * @see http://dev.mysql.com/doc/refman/5.7/en/blob.html
     */
    public function text($type = 'TEXT')
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder($type);
    }
}