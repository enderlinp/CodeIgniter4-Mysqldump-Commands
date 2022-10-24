<?php

/**
 * Mysqldump Spark Commands (CLI) for CodeIgniter 4.
 *
 * Tags: codeigniter4 spark commands mysqli mysqldump php7 php8 database php sql mysql-backup.
 *
 * @category Command Line
 * @author   enderlinp
 * @license  https://github.com/enderlinp/CodeIgniter4-Mysqldump-Commands/blob/main/LICENSE
 * @link     https://github.com/enderlinp/CodeIgniter4-Mysqldump-Commands
 *
 */

namespace App\Commands;

use CodeIgniter\Autoloader;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Ifsnop\Mysqldump as IMysqldump;

class Mysqldump extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'db:mysqldump';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Backup MySQL database to file.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'db:mysqldump [options]';

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
    	'--c' => 'Enable Gzip compression',
        '--d' => 'Add DROP TABLE statements',
        '--i' => 'Ignore all tables',
        '--r' => 'Reset AUTO_INCREMENT statements',
        '--u' => 'Set default character set to UTF8MB4',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $namespaces = service('autoloader')->getNamespace();
        
        // Check if `Ifsnop` namespace exists
        if (! isset($namespaces['Ifsnop'])) {
        	CLI::write('The namespace `Ifsnop` could not be found. Please run:', 'green');
        	CLI::write("\tcomposer require ifsnop/mysqldump-php", 'yellow');
        	
        	return;
        }
        
        try {
        	$db = Database::connect();
        	
        	if ($db->DBDriver !== 'MySQLi') {
        		CLI::error('The MySQLi driver is required.', 'light_gray', 'red');
	    		
	    		return;
	    	}
	    	
	    	$settings = [];
	    	$dbname   = $db->database;
	    	$hostname = $db->hostname;
	    	$username = $db->username;
	    	$password = $db->password;
	    	
	    	$filename = sprintf('%s_%s.sql', $dbname, date('Ymd_His'));
        	
        	// Enable Gzip compression
	    	if (CLI::getOption('c')) {
	    		$settings['compress'] = IMysqldump\Mysqldump::GZIP;
	    		
	    		// Append '.gz' to backup file extension
	    		$filename .= '.gz';
	    	}
    		
        	// Add DROP TABLE statements
    		if (CLI::getOption('d')) {
    			$settings['add-drop-table'] = true;
    		}
	    	
        	// Ignore all tables
    		if (CLI::getOption('i')) {
	    		$settings['no-data'] = true;
	    	}
	    	
	    	// Reset AUTO_INCREMENT statements
    		if (CLI::getOption('r')) {
	    		$settings['reset-auto-increment'] = true;
	    	}
	    	
	    	// Set default character set to UTF8MB4
        	if (CLI::getOption('u')) {
        		$settings['default-character-set'] = IMysqldump\Mysqldump::UTF8MB4;
        	}
        	
        	$directory = WRITEPATH . 'sql/';
        	
        	// Check if the `sql` directory exists, otherwise create it
        	if (! is_dir($directory)) {
        		if (CLI::prompt('The `sql` directory does not exist. Create it?', ['y', 'n']) === 'y') {
    				mkdir($directory, 0755, true);
    				
    				CLI::newLine();
    				CLI::write('`sql` directory created.', 'green');
    				CLI::newLine();
    			} else {
    				CLI::newLine();
    				CLI::error('Operation aborted.', 'light_gray', 'red');
    				
    				return;
    			}
    		}
    		
        	$dump = new IMysqldump\Mysqldump("mysql:host={$hostname};dbname={$dbname}", $username, $password, $settings);
        	$dump->start($directory . $filename);
        	
        	CLI::write("Database `{$dbname}` successfully backup.", 'green');
        }
        catch (\Exception $e) {
        	CLI::error($e->getMessage(), 'light_gray', 'red');
        }
    }
}
