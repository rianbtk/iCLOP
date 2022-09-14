-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 14 Sep 2022 pada 11.30
-- Versi server: 10.3.34-MariaDB-0ubuntu0.20.04.1
-- Versi PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tap`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `finish` ()  READS SQL DATA
BEGIN
    DECLARE msg TEXT DEFAULT _finish(
        _get('tnumb'),
        _get('plan'),
        num_failed()
    );
    if msg IS NOT NULL AND msg <> '' THEN SELECT msg; END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `no_plan` ()  BEGIN
    DECLARE hide TEXT DEFAULT plan(0);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `todo` (`how_many` INT, `why` TEXT)  BEGIN
    DECLARE hide INTEGER DEFAULT _add('todo', COALESCE(how_many, 1), COALESCE(why, ''));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `todo_end` ()  MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE tid INTEGER DEFAULT _get_latest_with_value( 'todo', -1 );
    DECLARE trash TEXT;
    IF tid IS NULL THEN
        CALL _cleanup();
        SELECT  `todo_end() called without todo_start()` INTO trash;
    END IF;
    DELETE FROM __tcache__ WHERE id = tid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `todo_start` (`why` TEXT)  BEGIN
    DECLARE hide INTEGER DEFAULT _add('todo', -1, COALESCE(why, ''));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `_cleanup` ()  MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    DELETE FROM __tcache__   WHERE cid = connection_id();
    DELETE FROM __tresults__ WHERE cid = connection_id();
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `_idset` (`vid` INTEGER, `vvalue` INTEGER)  MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    UPDATE __tcache__
       SET value = vvalue
     WHERE id = vid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `_populate_have` (IN `have` TEXT)  MODIFIES SQL DATA
    DETERMINISTIC
    COMMENT 'Create a temp table and populate with comma-separated data'
BEGIN
  DECLARE sep       CHAR(1) DEFAULT ',';
  DECLARE seplength INTEGER DEFAULT CHAR_LENGTH(sep);

  SET have = _fixCSL(have);

  DROP TEMPORARY TABLE IF EXISTS `have`;
  CREATE TEMPORARY TABLE `tap`.`have` (ident VARCHAR(64) PRIMARY KEY)
    ENGINE MEMORY CHARSET utf8 COLLATE utf8_general_ci;

  WHILE have != '' > 0 DO
    SET @val = TRIM(SUBSTRING_INDEX(have, sep, 1));
    SET @val = uqi(@val);
    IF  @val <> '' THEN
      INSERT IGNORE INTO `have` VALUE(@val);
    END IF;
    SET have = SUBSTRING(have, CHAR_LENGTH(@val) + seplength + 1);
  END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `_populate_want` (IN `want` TEXT)  MODIFIES SQL DATA
    DETERMINISTIC
    COMMENT 'Create a temp table and populate with comma-separated data'
BEGIN
  DECLARE sep       CHAR(1) DEFAULT ',';
  DECLARE seplength INTEGER DEFAULT CHAR_LENGTH(sep);

  SET want = _fixCSL(want);

  DROP TEMPORARY TABLE IF EXISTS `want`;
  CREATE TEMPORARY TABLE `tap`.`want` (ident VARCHAR(64) PRIMARY KEY)
    ENGINE MEMORY CHARSET utf8 COLLATE utf8_general_ci;

  WHILE want != '' > 0 DO
    SET @val = TRIM(SUBSTRING_INDEX(want, sep, 1));
    SET @val = uqi(@val);
    IF  @val <> '' THEN
      INSERT IGNORE INTO `want` VALUE(@val);
    END IF;
    SET want = SUBSTRING(want, CHAR_LENGTH(@val) + seplength + 1);
  END WHILE;
END$$

--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `add_result` (`vok` BOOL, `vaok` BOOL, `vdescr` TEXT, `vtype` TEXT, `vreason` TEXT) RETURNS INT(11) MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE tnumb INTEGER DEFAULT _nextnumb();
    INSERT INTO __tresults__ ( numb, cid, ok, aok, descr, type, reason )
    VALUES(tnumb, connection_id(), vok, vaok, coalesce(vdescr, ''), coalesce(vtype, ''), coalesce(vreason, ''));
    RETURN tnumb;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `alike` (`got` TEXT, `pat` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN _alike( got LIKE pat, got, pat, descr );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `check_test` (`have` TEXT, `eok` BOOLEAN, `name` TEXT, `edescr` TEXT, `ediag` TEXT, `matchit` BOOLEAN) RETURNS TEXT CHARSET utf8mb4 MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE tnumb   INTEGER DEFAULT _get('tnumb');
    DECLARE hok     BOOLEAN;
    DECLARE hdescr  TEXT;
    DECLARE ddescr  TEXT;
    DECLARE hdiag   TEXT;
    DECLARE tap     TEXT;
    DECLARE myok    BOOLEAN;

    
    SELECT aok, descr INTO hok ,hdescr
      FROM __tresults__ WHERE numb = tnumb;

    SET myok = CASE WHEN hok = eok THEN 1 ELSE 0 END;

    
    SET ddescr = concat(coalesce( concat(name, ' '), 'Test ' ), 'should ');

    
    UPDATE __tresults__
       SET ok     = myok,
           aok    = myok,
           descr  = concat(ddescr, CASE WHEN eok then 'pass' ELSE 'fail' END),
           type   = '',
           reason = ''
     WHERE numb = tnumb;
    SET tap = _tap(myok, tnumb, concat(ddescr, CASE WHEN eok then 'pass' ELSE 'fail' END), NULL);

    
    IF edescr IS NOT NULL THEN
        SET tap = concat(tap, '\n', eq(
            hdescr,
            edescr,
            concat(ddescr, 'have the proper description')
        ));
    END IF;

    
    IF ediag IS NOT NULL THEN
        
        SET hdiag = substring(
            have
            FROM (CASE WHEN hok THEN 4 ELSE 9 END) + char_length(tnumb)
        );

        
        IF hdescr <> '' THEN
            SET hdiag = substring( hdiag FROM 3 + char_length( diag( hdescr ) ) );
        END IF;

        
        IF NOT hok THEN
           SET hdiag = substring(
               hdiag
               FROM 14 + char_length(tnumb)
                       + CASE hdescr WHEN '' THEN 3 ELSE 3 + char_length( diag( hdescr ) ) END
           );
        END IF;

        
        SET hdiag = replace( substring(hdiag from 3), '\n# ', '\n' );

        
        IF matchit THEN
            SET tap = concat(tap, '\n', matches(
                hdiag,
                ediag,
                concat(ddescr, 'have the proper diagnostics')
            ));
        ELSE
            SET tap = concat(tap, '\n', eq(
                hdiag,
                ediag,
                concat(ddescr, 'have the proper diagnostics')
            ));
        END IF;
    END IF;

    
    RETURN tap;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `columns_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
 
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`',column_name,'`')
               FROM `information_schema`.`columns`
	       WHERE `table_schema` = sname
	       AND `table_name` = tname);

  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have the correct Columns');
  END IF;

  IF NOT _has_table(sname,tname) THEN
  RETURN CONCAT(ok(FALSE, description), '\n',
    diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('columns', @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `column_privileges_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `gtee` VARCHAR(81), `ptypes` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
 
  SET @gtee = _format_user(gtee);
  SET @want = ptypes;
  SET @have = _column_privileges(sname, tname, cname, @gtee);

  IF description = '' THEN
    SET description = CONCAT('Account ', gtee, ' should have the correct column privileges');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Column `', tname,'`.`', cname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('Column Privileges', @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_character_set_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `cset` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN col_charset_is(sname, tname, cname, cset, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_charset_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `cset` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
      ' should have Character Set ' , quote_ident(cset));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_col_charset(sname, tname, cname), cset, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_collation_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `ccoll` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.',
      quote_ident(cname), ' should have collation ' , quote_ident(ccoll));
  END IF;

  IF NOT _has_column(sname, tname, cname)THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_col_collation(sname, tname, cname), ccoll, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_column_type_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `ctype` LONGTEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
      ' should have Column Type ', qv(ctype));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description),'\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_column_type(sname, tname, cname), ctype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_data_type_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `dtype` LONGTEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
      ' should have Data Type ', qv(dtype));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description),'\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_data_type(sname, tname, cname), dtype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_default_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `cdefault` LONGTEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
      ' should have Default ', qv(cdefault));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description),'\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_col_default(sname, tname, cname), cdefault, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_extra_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `cextra` VARCHAR(30), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' should have Extra ', quote_ident(cextra));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
       diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
         ' does not exist')));
  END IF;

  RETURN eq(_col_extra_is(sname, tname, cname), cextra, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_default` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
   SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should not have a default');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description),'\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_default(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_index_key` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
        quote_ident(tname), '.', quote_ident(cname), ' should not have Index Key');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_index_key(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_named_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `kname` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET kname := COALESCE(kname, cname); 
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
      ' should not have INDEX Key ', quote_ident(kname));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_named_index(sname, tname, cname, kname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_non_unique_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
       quote_ident(tname), '.', quote_ident(cname), ' should not have non unique INDEX');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_non_unique_index(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_pos_in_named_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `kname` VARCHAR(64), `pos` INT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET kname := COALESCE(kname, cname); 

  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should not have position ',
        pos, ' in INDEX ', quote_ident(kname));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_pos_in_named_index(sname, tname, cname, kname, pos), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_primary_key` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should not be a Primary Key (or part thereof)');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_primary_key(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_hasnt_unique_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should not have unique INDEX');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _col_has_unique_index(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_default` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should have a default');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description),'\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(_col_has_default(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_index_key` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should have Index Key');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(_col_has_index_key(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_named_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `kname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 NO SQL
    DETERMINISTIC
BEGIN
  SET kname := COALESCE(kname, cname); 

  IF description = '' THEN
    SET description = concat('Column ', quote_ident(tname), '.', quote_ident(cname),
      ' should have Index Key ', quote_ident(kname));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
' does not exist')));
  END IF;

  RETURN ok(_col_has_named_index(sname, tname, cname, kname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_non_unique_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should have non unique INDEX');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok( _col_has_non_unique_index(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_pos_in_named_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `kname` VARCHAR(64), `pos` INT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET kname := COALESCE(kname, cname); 

  IF description = '' THEN
    SET description = concat('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should have position ',
        pos, ' in Index ', quote_ident(kname));
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(_col_has_pos_in_named_index(sname, tname, cname, kname, pos), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_primary_key` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should be a Primary Key (or part thereof)');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(_col_has_primary_key(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `ctype` LONGTEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 NO SQL
    DETERMINISTIC
BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' should have Column Type ', qv(ctype));
    END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(_col_has_type(sname, tname, cname, ctype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_has_unique_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should have unique INDEX');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN ok(_col_has_unique_index(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_is_null` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should allow NULL');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_col_nullable(sname, tname, cname),'YES', description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_is_pk` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF NOT LOCATE(',', want) AND NOT LOCATE('`', want) THEN
    SET want = CONCAT('`', want, '`'); 
  END IF;

  IF description = '' THEN
    SET description = CONCAT('Primary Key for ', quote_ident(sname), '.', quote_ident(tname),
      ' should exist on ', want);
  END IF;

  IF NOT _has_table( sname, tname ) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' does not exist')));
  END IF;

  RETURN ok(_col_is_pk( sname, tname, want), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_is_unique` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  IF NOT LOCATE(',', want) AND NOT LOCATE('`', want) THEN
    SET want = CONCAT('`', want, '`');
  END IF;

  IF description = '' THEN
    SET description = CONCAT('Unique Index for ', quote_ident(sname), '.', quote_ident(tname),
      ' should exist on ', want);
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;
  
  RETURN ok(_col_is_unique( sname, tname, want), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `col_not_null` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should be NOT NULL');
  END IF;

  IF NOT _has_column(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Column ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_col_nullable(sname, tname, cname), 'NO', description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `constraints_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`', `constraint_name`,'`')
               FROM `information_schema`.`table_constraints`
               WHERE `table_schema` = sname
               AND `table_name` = tname);

  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have the correct Constraints');
  END IF;

  IF NOT _has_table( sname, tname ) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist' )));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('constraints', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `constraint_type_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `ctype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
      ' should have Constraint Type ' , qv(ctype));
  END IF;
    
  IF NOT _has_constraint(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_constraint_type(sname, tname, cname), ctype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `diag` (`msg` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN concat('# ', replace(
       replace(
            replace( msg, '\r\n', '\n# ' ),
            '\n',
            '\n# '
        ),
        '\r',
        '\n# '
    ));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `doesnt_match` (`got` TEXT, `pat` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN _unalike( got NOT REGEXP pat, got, pat, descr );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `dqv` (`val` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF ISNULL(val) THEN
      RETURN 'NULL';
    END IF;

    
    IF val REGEXP '^[[:digit:]]+$' THEN
      RETURN val;
    END IF;

    RETURN CONCAT('"', REPLACE(val, '''', '\\\''), '"');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `engine_is_default` (`ename` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
  SET description = CONCAT('Storage Engine ', quote_ident(ename),
    ' should be the default');
  END IF;

  IF NOT _has_engine(ename) THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag (CONCAT('Storage engine ', quote_ident(ename), ' is not available')));
  END IF;

  RETURN eq(_engine_default(), ename, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `eq` (`have` TEXT, `want` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF _eq(have, want) THEN RETURN ok(1, descr); END IF;

    
    RETURN concat( ok(0, descr), '\n', diag(concat(
           '        have: ', COALESCE(have, 'NULL'),
         '\n        want: ', COALESCE(want, 'NULL')
    )));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `events_are` (`sname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`',`event_name`,'`')
               FROM `information_schema`.`events`
               WHERE `event_schema` = sname);

  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname), ' should have the correct Events');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT( ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist' )));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('events', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `event_interval_field_is` (`sname` VARCHAR(64), `ename` VARCHAR(64), `ifield` VARCHAR(18), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE valid ENUM('YEAR','QUARTER','MONTH','DAY','HOUR','MINUTE ',
              'WEEK','SECOND','YEAR_MONTH','DAY_HOUR','DAY_MINUTE',
	      'DAY_SECOND','HOUR_MINUTE','HOUR_SECOND','MINUTE_SECOND');
  
  DECLARE CONTINUE HANDLER FOR 1265
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag('Event Interval must be { YEAR | QUARTER | MONTH | DAY | HOUR | MINUTE |
              WEEK | SECOND | YEAR_MONTH | DAY_HOUR | DAY_MINUTE |
              DAY_SECOND | HOUR_MINUTE | HOUR_SECOND | MINUTE_SECOND }'));
  
  IF description = '' THEN
    SET description = CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
      ' should have Interval Field ', qv(ifield));
  END IF;

  SET valid = ifield;

  IF NOT _has_event(sname,ename) THEN
    RETURN CONCAT(ok(FALSE, description), '\n', 
      diag(CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
        ' does not exist')));
    END IF;

    RETURN eq(_event_interval_field(sname, ename), ifield, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `event_interval_value_is` (`sname` VARCHAR(64), `ename` VARCHAR(64), `ivalue` VARCHAR(256), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
      ' should have Interval Value ', qv(ivalue));
  END IF;

  IF NOT _has_event(sname,ename) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
        ' does not exist')));
  END IF;

  RETURN eq(_event_interval_value(sname, ename), ivalue, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `event_status_is` (`sname` VARCHAR(64), `ename` VARCHAR(64), `stat` VARCHAR(18), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE valid ENUM('ENABLED','DISABLED','SLAVESIDE DISABLED');
  
  DECLARE CONTINUE HANDLER FOR 1265
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag('Event Status must be { ENABLED | DISABLED | SLAVESIDE DISABLED }'));

  IF description = '' THEN
    SET description = CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
      ' should have Status ', qv(stat));
  END IF;

  SET valid = stat;

  IF NOT _has_event(sname,ename) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
        ' does not exist')));
  END IF;

  RETURN eq(_event_status(sname, ename), stat, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `event_type_is` (`sname` VARCHAR(64), `ename` VARCHAR(64), `etype` VARCHAR(9), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE valid ENUM('ONE TIME','RECURRING');
  
  DECLARE CONTINUE HANDLER FOR 1265
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag('Event Type must be { ONE TIME | RECURRING }'));
  
  IF description = '' THEN
    SET description = CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
      ' should have Event Type ', qv(etype));
  END IF;

  SET valid = etype;

  IF NOT _has_event(sname,ename) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
        ' does not exist')));
  END IF;

  RETURN eq(_event_type(sname, ename), etype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fail` (`descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN ok(0, descr);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fk_ok` (`csch` VARCHAR(64), `ctab` VARCHAR(64), `ccol` TEXT, `usch` VARCHAR(64), `utab` VARCHAR(64), `ucol` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN

  IF NOT LOCATE(',', ccol) AND NOT LOCATE('`', ccol) THEN
    SET ccol = CONCAT('`', ccol, '`');
  END IF;

  IF NOT LOCATE(',', ucol) AND NOT LOCATE('`', ucol) THEN
    SET ucol = CONCAT('`', ucol, '`');
  END IF;

  IF description = '' THEN
    SET description = CONCAT('Constraint Foreign Key ', quote_ident(ctab), '(', ccol,
      ') should reference ' , quote_ident(utab), '(', ucol, ')');
  END IF;

  IF NOT _has_table(csch, ctab) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(csch), '.', quote_ident(ctab),
        ' does not exist')));
  END IF;

  IF NOT _has_table(usch, utab) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(usch), '.', quote_ident(utab),
        ' does not exist')));
  END IF;

  RETURN ok(_fk_ok(csch, ctab, ccol, usch, utab, ucol), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fk_on_delete` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `rule` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
      ' should have rule ON DELETE ', qv(rule));
  END IF;

  IF NOT _has_constraint(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_fk_on_delete(sname, tname, cname), rule, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fk_on_update` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `rule` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
      ' should have rule ON UPDATE ' , qv(rule));
  END IF;

  IF NOT _has_constraint(sname, tname, cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
        ' does not exist')));
  END IF;

  RETURN eq(_fk_on_update(sname, tname, cname), rule, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `function_data_type_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `dtype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Function ', quote_ident(sname), '.', quote_ident(rname),
      ' should return ', quote_ident(_datatype(dtype)));
  END IF;

  IF NOT _has_routine(sname, rname, 'FUNCTION') THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Function ', quote_ident(sname),'.', quote_ident(rname),
        ' does not exist')));
  END IF;

  RETURN eq(_function_data_type(sname, rname), _datatype(dtype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `function_is_deterministic` (`sname` VARCHAR(64), `rname` VARCHAR(64), `val` VARCHAR(3), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN routine_is_deterministic(sname, rname, 'Function', val, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `function_security_type_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `stype` VARCHAR(7), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN routine_security_type_is(sname, rname, 'Function', stype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `function_sql_data_access_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `sda` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN routine_sql_data_access_is(sname, rname, 'Function', sda, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `global_is` (`var` VARCHAR(64), `want` VARCHAR(1024), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('@@GLOBAL.' , var, ' should be correctly set');
  END IF;

  IF NOT tap.mysql_version() >= 507006 THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag (CONCAT('This version of MySQL requires the previous version of this function')));
  END IF;

  RETURN eq(_global_var(var), want, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `global_privileges_are` (`gtee` VARCHAR(81), `ptypes` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
 
  SET @gtee = _format_user(gtee);
  SET @want = ptypes;
  SET @have = _global_privileges(@gtee);

  IF description = '' THEN
    SET description = CONCAT('Account ', gtee, ' should have the correct global privileges');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('Global Privileges', @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_character_set` (`cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN hasnt_charset(cname, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_charset` (`cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Character Set ', quote_ident(cname), ' should not be available' );
  END IF;

  RETURN ok(NOT _has_charset(cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_collation` (`cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Collation ', quote_ident(cname), ' should not be available');
  END IF;

  RETURN ok(NOT _has_collation(cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_column` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should not exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

RETURN ok(NOT _has_column(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_column_privilege` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee,
       ' should not have column privilege ''', ptype, '''');
  END IF;

  IF NOT _has_column(sname,tname,cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Column `', tname, '`.`', cname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _column_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid column privilege type')));
  END IF;

  RETURN ok(NOT _has_column_priv(sname, tname, cname, @gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_constraint` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Constraint ', quote_ident(tname),'.',quote_ident(cname),
      ' should not exist');
  END IF;

  IF NOT _has_table( sname, tname ) THEN
    RETURN CONCAT( ok( FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _has_constraint(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_event` (`sname` VARCHAR(64), `ename` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
      ' should not exist');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
    END IF;

  RETURN ok(NOT _has_event(sname, ename), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_fk` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should not have a Foreign Key');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _has_constraint_type(sname, tname, 'FOREIGN KEY'), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_function` (`sname` VARCHAR(64), `rname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN hasnt_routine(sname, rname, 'Function', description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_global_privilege` (`gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should not have global privilege ''', ptype, '''');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _global_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid global privilege type')));
  END IF;

  RETURN ok(NOT _has_global_priv(@gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Index ', quote_ident(tname), '.', quote_ident(iname),
      ' should not exist');
  END IF;
    
  IF NOT _has_table( sname, tname ) THEN
    RETURN CONCAT( ok( FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(NOT _has_index(sname, tname, iname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_partition` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Partition ', quote_ident(tname), '.', quote_ident(part),
      ' should not exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
    END IF;

    RETURN ok(NOT _has_partition(sname, tname, part), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_pk` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should not have a Primary Key');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;
  
  RETURN ok(NOT _has_constraint(sname, tname, 'PRIMARY'), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_privilege` (`gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should not have privilege ''', ptype, '''');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  RETURN ok(NOT _has_priv(@gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_procedure` (`sname` VARCHAR(64), `rname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN hasnt_routine(sname, rname, 'Procedure', description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_role` (`rname` CHAR(97), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @rname = _format_user(rname);

  IF description = '' THEN
    SET description = CONCAT('Role ', rname, ' should not be active');
  END IF;

  
  RETURN ok(NOT _has_role(@rname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_routine` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT(rtype ,' ',
      quote_ident(sname), '.', quote_ident(rname), ' should not exist');
  END IF;

  RETURN ok(NOT _has_routine(sname, rname, rtype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_schema` (`sname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname), ' should not exist');
  END IF;

  RETURN ok(NOT _has_schema(sname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_schema_privilege` (`sname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should not have schema privilege ''', ptype, '''');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', sname, ' does not exist')));
  END IF;

  IF NOT _schema_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid schema privilege type')));
  END IF;

  RETURN ok(NOT _has_schema_priv(sname, @gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_subpartition` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Subpartition ', quote_ident(tname),
       '.', quote_ident(subp), ' should not exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
    END IF;

  RETURN ok(NOT _has_subpartition(sname, tname, subp), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_table` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ',
      quote_ident(sname), '.', quote_ident(tname), ' should not exist');
  END IF;

  RETURN ok(NOT _has_table(sname, tname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_table_privilege` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should not have table privilege ''', ptype, '''');
  END IF;

  IF NOT _has_table(sname,tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table `', sname, '`.`', tname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _table_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid table privilege type')));
  END IF;

  RETURN ok(NOT _has_table_priv(sname, tname, @gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_timezones` (`description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Table `mysql`.`time_zone_data` should be empty');
  END IF;

  RETURN ok(NOT _has_timezones(), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_trigger` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Trigger ', quote_ident(tname), '.', quote_ident(trgr),
      ' should not exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
    END IF;

    RETURN ok(NOT _has_trigger(sname, tname, trgr), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_user` (`hname` CHAR(60), `uname` CHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('User \'', uname, '\'@\'', hname, '\' should not exist');
  END IF;

  RETURN ok(NOT _has_user(hname, uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_user_at_host` (`uname` CHAR(97), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN

  SET @uname = _format_user(uname);

  IF description = '' THEN
    SET description = CONCAT('User ', uname, ' should not exist');
  END IF;

  RETURN ok(NOT _has_user_at_host(@uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hasnt_view` (`sname` VARCHAR(64), `vname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ',
      quote_ident(sname), '.', quote_ident(vname), ' should not exist' );
  END IF;

  RETURN ok(NOT _has_view(sname, vname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_character_set` (`cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN has_charset(cname, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_charset` (`cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Character Set ', quote_ident(cname), ' should be available');
  END IF;

  RETURN ok(_has_charset(cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_collation` (`cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Collation ', quote_ident(cname), ' should be available');
  END IF; 

  RETURN ok(_has_collation(cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_column` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Column ',
      quote_ident(tname), '.', quote_ident(cname), ' should exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_column(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_column_privilege` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should have column privilege ''', ptype, '''');
  END IF;

  IF NOT _has_column(sname,tname,cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Column `', tname, '`.`', cname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _column_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid column privilege type')));
  END IF;

  RETURN ok(_has_column_priv(sname, tname, cname, @gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_constraint` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Constraint ', quote_ident(tname), '.', quote_ident(cname),
      ' should exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_constraint(sname, tname, cname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_engine` (`ename` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Storage Engine ', quote_ident(ename), ' should be available');
  END IF;

  RETURN ok(_has_engine(ename), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_event` (`sname` VARCHAR(64), `ename` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Event ', quote_ident(sname), '.', quote_ident(ename),
      ' should exist');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
    END IF;

    RETURN ok(_has_event(sname, ename), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_fk` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have a Foreign Key');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_constraint_type(sname, tname, 'FOREIGN KEY'), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_function` (`sname` VARCHAR(64), `rname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN has_routine(sname, rname, 'Function', description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_global_privilege` (`gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should have global privilege ''', ptype, '''');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _global_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid global privilege type')));
  END IF;

  RETURN ok(_has_global_priv(@gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Index ', quote_ident(tname), '.', quote_ident(iname),
      ' should exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_index( sname, tname, iname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_partition` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Partition ', quote_ident(tname), '.', quote_ident(part),
      ' should exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

    RETURN ok(_has_partition(sname, tname, part), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_partitioning` (`description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = 'Partitioning should be active';
  END IF;

  IF tap.mysql_version() >= 800011 THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag('Partitioning support is part of specific ENGINE post 8.0.11'));
  END IF;

RETURN ok(_has_partitioning(), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_pk` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have a Primary Key');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_constraint(sname, tname, 'PRIMARY'), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_privilege` (`gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = CONCAT('Account ', gtee, ' should have privilege ''', ptype, '''');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag (CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  RETURN ok(_has_priv(@gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_procedure` (`sname` VARCHAR(64), `rname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN has_routine(sname, rname, 'Procedure', description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_role` (`rname` CHAR(97), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @rname = _format_user(rname);

  IF description = '' THEN
    SET description = CONCAT('Role ', rname, ' should be active');
  END IF;

  IF NOT _has_user_at_host(@rname) THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag (CONCAT('Role ', rname, ' is not defined')));
  END IF;

  RETURN ok(_has_role(@rname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_routine` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT(rtype ,' ',
      quote_ident(sname), '.', quote_ident(rname), ' should exist');
  END IF;

  RETURN ok(_has_routine(sname, rname, rtype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_schema` (`sname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname), ' should exist');
  END IF;

  RETURN ok(_has_schema(sname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_schema_privilege` (`sname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should have schema privilege ''', ptype, '''');
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', sname, ' does not exist')));
  END IF;

  IF NOT _schema_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid schema privilege type')));
  END IF;

  RETURN ok(_has_schema_priv(sname, @gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_security_definer` (`sname` VARCHAR(64), `vname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ',
      quote_ident(sname), '.', quote_ident(vname), ' should have security DEFINER');
  END IF;

  IF NOT _has_view(sname, vname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_security(sname, vname, 'DEFINER'), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_security_invoker` (`sname` VARCHAR(64), `vname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ',
      quote_ident(sname), '.', quote_ident(vname), ' should have security INVOKER');
  END IF;

  IF NOT _has_view(sname, vname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
        ' does not exist')));
  END IF;
  
  RETURN ok(_has_security(sname, vname, 'INVOKER'), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_subpartition` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Subpartition ', quote_ident(tname),
      '.' , quote_ident(subp), ' should exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_subpartition(sname, tname, subp), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_table` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Table ',
      quote_ident(sname), '.', quote_ident(tname), ' should exist');
  END IF;

  RETURN ok(_has_table(sname, tname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_table_privilege` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should have table privilege ''', ptype, '''');
  END IF;

  IF NOT _has_table(sname,tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table `', sname, '`.`', tname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  IF NOT _table_privs(ptype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Privilege ''', ptype, ''' is not a valid table privilege type')));
  END IF;

  RETURN ok(_has_table_priv(sname, tname, @gtee, ptype), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_timezones` (`description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Table `mysql`.`time_zone_data` should be populated');
  END IF; 

  RETURN ok(_has_timezones(), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_trigger` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Trigger ', quote_ident(tname), '.', quote_ident(trgr),
      ' should exist');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

    RETURN ok(_has_trigger(sname, tname, trgr), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_unique` (`sname` VARCHAR(64), `tname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN 
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have a Unique Index');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN ok(_has_unique(sname, tname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_user` (`hname` CHAR(60), `uname` CHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('User \'', uname, '\'@\'', quote_ident(hname), '\' should exist');
  END IF;

  RETURN ok(_has_user (hname, uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_user_at_host` (`uname` CHAR(97), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN

  SET @uname = _format_user(uname);

  IF description = '' THEN
    SET description = CONCAT('User ', uname, ' should exist');
  END IF;

  RETURN ok(_has_user_at_host(@uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `has_view` (`sname` VARCHAR(64), `vname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ',
      quote_ident(sname), '.', quote_ident(vname), ' should exist');
  END IF;

  RETURN ok(_has_view(sname, vname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `indexes_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`', s.`index_name`,'`')
               FROM `information_schema`.`statistics` s
               LEFT JOIN `information_schema`.`table_constraints` c
               ON (s.`table_schema` = c.`table_schema`
                   AND s.`table_name` = c.`table_name` 
                   AND s.`index_name` = c.`constraint_name`)
               WHERE s.`table_schema` = sname
               AND s.`table_name` = tname
               AND c.`constraint_name` IS NULL);

  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have the correct indexes');
  END IF;
    
  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n', 
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist' )));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('indexes', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `index_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Index ', quote_ident(tname), '.', quote_ident(iname),
      ' should exist on ' , want);
  END IF;

  IF NOT _has_index(sname, tname, iname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Index ', quote_ident(tname), '.', quote_ident(iname),
        ' does not exist' )));
  END IF;

  RETURN eq(_index_def(sname, tname, iname), want, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `index_is_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64), `itype` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
     SET description = CONCAT('Index ', quote_ident(tname), '.', quote_ident(iname),
      ' should be of Type ', qv(itype));
  END IF;

  IF NOT _has_table( sname, tname ) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        'does not exist' )));
  END IF;


  
  IF NOT _has_index( sname, tname, iname ) THEN
    RETURN CONCAT(ok(FALSE,description),'\n',
      diag(CONCAT('Index ', quote_ident(tname), '.', quote_ident(iname), 
        ' does not exist')));
  END IF;

  RETURN eq(_index_type( sname, tname, iname), itype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `in_todo` () RETURNS TINYINT(1) READS SQL DATA
BEGIN
    RETURN CASE WHEN _get('todo') IS NULL THEN 0 ELSE 1 END;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `is_indexed` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN 
    SET description = CONCAT('Index for ', quote_ident(sname), '.', quote_ident(tname),
      ' should exist on ' , want);
  END IF;

  IF NOT _has_table( sname, tname ) THEN
    RETURN CONCAT(ok( FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist' )));
  END IF;

  RETURN ok(_is_indexed(sname, tname, want), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `is_reserved` (`word` TEXT) RETURNS TINYINT(1) BEGIN
    RETURN UPPER(word) IN (
        'ADD',                    'ALL',                    'ALTER',
        'ANALYZE',                'AND',                    'AS',
        'ASC',                    'ASENSITIVE',             'BEFORE',
        'BETWEEN',                'BIGINT',                 'BINARY',
        'BLOB',                   'BOTH',                   'BY',
        'CALL',                   'CASCADE',                'CASE',
        'CHANGE',                 'CHAR',                   'CHARACTER',
        'CHECK',                  'COLLATE',                'COLUMN',
        'CONDITION',              'CONSTRAINT',             'CONTINUE',
        'CONVERT',                'CREATE',                 'CROSS',
        'CURRENT_DATE',           'CURRENT_TIME',           'CURRENT_TIMESTAMP',
        'CURRENT_USER',           'CURSOR',                 'DATABASE',
        'DATABASES',              'DAY_HOUR',               'DAY_MICROSECOND',
        'DAY_MINUTE',             'DAY_SECOND',             'DEC',
        'DECIMAL',                'DECLARE',                'DEFAULT',
        'DELAYED',                'DELETE',                 'DESC',
        'DESCRIBE',               'DETERMINISTIC',          'DISTINCT',
        'DISTINCTROW',            'DIV',                    'DOUBLE',
        'DROP',                   'DUAL',                   'EACH',
        'ELSE',                   'ELSEIF',                 'ENCLOSED',
        'ESCAPED',                'EXISTS',                 'EXIT',
        'EXPLAIN',                'FALSE',                  'FETCH',
        'FLOAT',                  'FLOAT4',                 'FLOAT8',
        'FOR',                    'FORCE',                  'FOREIGN',
        'FROM',                   'FULLTEXT',               'GRANT',
        'GROUP',                  'HAVING',                 'HIGH_PRIORITY',
        'HOUR_MICROSECOND',       'HOUR_MINUTE',            'HOUR_SECOND',
        'IF',                     'IGNORE',                 'IN',
        'INDEX',                  'INFILE',                 'INNER',
        'INOUT',                  'INSENSITIVE',            'INSERT',
        'INT',                    'INT1',                   'INT2',
        'INT3',                   'INT4',                   'INT8',
        'INTEGER',                'INTERVAL',               'INTO',
        'IS',                     'ITERATE',                'JOIN',
        'KEY',                    'KEYS',                   'KILL',
        'LEADING',                'LEAVE',                  'LEFT',
        'LIKE',                   'LIMIT',                  'LINES',
        'LOAD',                   'LOCALTIME',              'LOCALTIMESTAMP',
        'LOCK',                   'LONG',                   'LONGBLOB',
        'LONGTEXT',               'LOOP',                   'LOW_PRIORITY',
        'MATCH',                  'MEDIUMBLOB',             'MEDIUMINT',
        'MEDIUMTEXT',             'MIDDLEINT',              'MINUTE_MICROSECOND',
        'MINUTE_SECOND',          'MOD',                    'MODIFIES',
        'NATURAL',                'NOT',                    'NO_WRITE_TO_BINLOG',
        'NULL',                   'NUMERIC',                'ON',
        'OPTIMIZE',               'OPTION',                 'OPTIONALLY',
        'OR',                     'ORDER',                  'OUT',
        'OUTER',                  'OUTFILE',                'PRECISION',
        'PRIMARY',                'PROCEDURE',              'PURGE',
        'RAID0',                  'READ',                   'READS',
        'REAL',                   'REFERENCES',             'REGEXP',
        'RELEASE',                'RENAME',                 'REPEAT',
        'REPLACE',                'REQUIRE',                'RESTRICT',
        'RETURN',                 'REVOKE',                 'RIGHT',
        'RLIKE',                  'SCHEMA',                 'SCHEMAS',
        'SECOND_MICROSECOND',     'SELECT',                 'SENSITIVE',
        'SEPARATOR',              'SET',                    'SHOW',
        'SMALLINT',               'SONAME',                 'SPATIAL',
        'SPECIFIC',               'SQL',                    'SQLEXCEPTION',
        'SQLSTATE',               'SQLWARNING',             'SQL_BIG_RESULT',
        'SQL_CALC_FOUND_ROWS',    'SQL_SMALL_RESULT',       'SSL',
        'STARTING',               'STRAIGHT_JOIN',          'TABLE',
        'TERMINATED',             'THEN',                   'TINYBLOB',
        'TINYINT',                'TINYTEXT',               'TO',
        'TRAILING',               'TRIGGER',                'TRUE',
        'UNDO',                   'UNION',                  'UNIQUE',
        'UNLOCK',                 'UNSIGNED',               'UPDATE',
        'USAGE',                  'USE',                    'USING',
        'UTC_DATE',               'UTC_TIME',               'UTC_TIMESTAMP',
        'VALUES',                 'VARBINARY',              'VARCHAR',
        'VARCHARACTER',           'VARYING',                'WHEN',
        'WHERE',                  'WHILE',                  'WITH',
        'WRITE',                  'X509',                   'XOR',
        'YEAR_MONTH',             'ZEROFILL',
        'ASENSITIVE',             'CALL',                   'CONDITION',
        'CONTINUE',               'CURSOR',                 'DECLARE',
        'DETERMINISTIC',          'EACH',                   'ELSEIF',
        'EXIT',                   'FETCH',                  'INOUT',
        'INSENSITIVE',            'ITERATE',                'LEAVE',
        'LOOP',                   'MODIFIES',               'OUT',
        'READS',                  'RELEASE',                'REPEAT',
        'RETURN',                 'SCHEMA',                 'SCHEMAS',
        'SENSITIVE',              'SPECIFIC',               'SQL',
        'SQLEXCEPTION',           'SQLSTATE',               'SQLWARNING',
        'TRIGGER',                'UNDO',                   'WHILE',
        'ACTION', 'BIT', 'DATE', 'ENUM', 'NO', 'TEXT', 'TIME', 'TIMESTAMP'
    );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `matches` (`got` TEXT, `pat` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN _alike( got REGEXP pat, got, pat, descr );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `mysql_variant` () RETURNS VARCHAR(7) CHARSET utf8mb4 BEGIN
   RETURN (SELECT
           CASE
	     WHEN version() REGEXP 'MariaDB' = 1 THEN 'MariaDB'
	     WHEN version() REGEXP 'Percona' = 1 THEN 'Percona'
	     ELSE 'MySQL'
	   END);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `mysql_version` () RETURNS INT(11) BEGIN
    RETURN (substring_index(version(), '.', 1) * 100000)
         + (substring_index(substring_index(version(), '.', 2), '.', -1) * 1000)
         + CAST(substring_index(substring_index(substring_index(version(), '-', 1),'.', 3), '.', -1) AS UNSIGNED);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `mytap_version` () RETURNS VARCHAR(10) CHARSET utf8mb4 NO SQL
    DETERMINISTIC
BEGIN
    RETURN '1.0';
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `not_eq` (`have` TEXT, `want` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF NOT _eq(have, want) THEN RETURN ok(1, descr); END IF;

    
    RETURN concat( ok(0, descr), '\n', diag(concat(
           '        have: ', COALESCE(have, 'NULL'),
         '\n        want: anything else'
    )));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `num_failed` () RETURNS INT(11) READS SQL DATA
BEGIN
    DECLARE ret integer;
    SELECT COUNT(*) INTO ret
      FROM __tresults__
     WHERE cid = connection_id()
       AND ok  = 0;
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `ok` (`aok` BOOLEAN, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE todo_why TEXT DEFAULT _todo();
    DECLARE ok BOOLEAN;
    DECLARE test_num INTEGER;

    SET ok = CASE
        WHEN aok THEN aok
        WHEN todo_why IS NULL THEN COALESCE(aok, 0)
        ELSE 1
    END;

    SET test_num = add_result(
        ok,
        COALESCE(aok, false),
        descr,
        CASE WHEN todo_why IS NULL THEN '' ELSE 'todo' END,
        COALESCE(todo_why, '')
    );

    RETURN _tap(aok, test_num, descr, todo_why);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partitions_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`', COALESCE(`subpartition_name`, `partition_name`) ,'`')
               FROM `information_schema`.`partitions`
               WHERE `table_schema` = sname
               AND `table_name` = tname);
	  
  IF description = '' THEN 
     SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have the correct partitions');
  END IF;

  IF NOT _has_table(sname,tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('partitions', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partition_count_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cnt` SMALLINT, `description` TEXT) RETURNS LONGTEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have a Partition Count of ', qv(cnt));
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok( FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname),'.', quote_ident(tname),
        ' does not exist')));
  END IF;

  RETURN eq(_partition_count(sname, tname), cnt, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partition_expression_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64), `expr` LONGTEXT, `description` TEXT) RETURNS LONGTEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Partition ', quote_ident(tname), '.', quote_ident(part),
      ' should have Partition Expression ', qv(TRIM(expr)));
  END IF;

  IF NOT _has_partition(sname, tname, part) THEN
    RETURN CONCAT(ok( FALSE, description), '\n',
      diag(CONCAT('Partition ', quote_ident(tname),'.', quote_ident(part),
        ' does not exist')));
  END IF;

  RETURN eq(_partition_expression(sname, tname, part), TRIM(expr), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `partition_method_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64), `pmeth` VARCHAR(18), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE valid ENUM('RANGE', 'LIST', 'HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY');
  
  DECLARE EXIT HANDLER FOR 1265
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag('Partitioning Method must be { RANGE | LIST | HASH | LINEAR HASH | KEY | LINEAR KEY }'));

  IF description = '' THEN
    SET description = CONCAT('Partition ', quote_ident(tname), '.', quote_ident(part),
      ' should have Partition Method ', qv(pmeth));
  END IF;

  SET valid = pmeth;

  IF NOT _has_partition(sname, tname, part) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Partition ', quote_ident(tname),'.', quote_ident(part),
        ' does not exist')));
  END IF;

  RETURN eq(_partition_method(sname, tname, part), pmeth, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `pass` (`descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN ok(1, descr);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `plan` (`numb` INTEGER) RETURNS TEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE trash TEXT;
    IF _get('plan') IS NOT NULL THEN
        CALL _cleanup();
        
        SELECT `You tried to plan twice!` INTO trash;
    END IF;

    RETURN concat('1..', _set('plan', numb, NULL ));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `procedure_is_deterministic` (`sname` VARCHAR(64), `rname` VARCHAR(64), `val` VARCHAR(3), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN routine_is_deterministic(sname, rname, 'Procedure', val, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `procedure_security_type_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `stype` VARCHAR(7), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN routine_security_type_is(sname, rname, 'Procedure', stype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `procedure_sql_data_access_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `sda` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN routine_sql_data_access_is(sname, rname, 'Procedure', sda, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `qi` (`ident` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN






  IF LEFT(ident,1) = '`' AND RIGHT(ident,1) = '`' THEN
	  RETURN ident;
  END IF;

  IF LEFT(ident,1) = '"' AND RIGHT(ident,1) = '"' THEN
	  RETURN CONCAT('`', TRIM(BOTH '"' FROM ident) ,'`');
  END IF;

  RETURN CONCAT('`', ident, '`');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `qs` (`val` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF ISNULL(val) THEN
      RETURN 'NULL';
    END IF;

    RETURN CONCAT('\'', REPLACE(val, '''', '\\\''), '\'');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `quote_ident` (`ident` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF ISNULL(ident) THEN
      RETURN 'NULL';
    END IF;

    IF ident = '' THEN
      RETURN '\'\'';
    END IF;

    IF LOCATE('ANSI_QUOTES', @@SQL_MODE) > 0 THEN
        IF is_reserved(ident) OR locate('"', ident) > 0 THEN
            RETURN concat('"', replace(ident, '"', '""'), '"');
        END IF;
    ELSE
        IF is_reserved(ident) OR locate('`', ident) > 0 THEN
            RETURN concat('`', replace(ident, '`', '``'), '`');
        END IF;
    END IF;

    RETURN ident;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `qv` (`val` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF ISNULL(val) THEN
      RETURN 'NULL';
    END IF;

    
    IF val REGEXP '^[[:digit:]]+$' THEN
      RETURN val;
    END IF;

    RETURN CONCAT('\'', REPLACE(val, '''', '\\\''), '\'');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `role_isnt_default` (`rname` CHAR(97), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @rname = _format_user(rname);

  IF description = '' THEN
    SET description = CONCAT('Role ', rname, ' should not be a DEFAULT role');
  END IF;

  
  RETURN ok(NOT _role_is_default(@rname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `role_is_default` (`rname` CHAR(97), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @rname = _format_user(rname);

  IF description = '' THEN
    SET description = CONCAT('Role ', rname, ' should be a DEFAULT role');
  END IF;

  IF NOT _has_user_at_host(@rname) THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag (CONCAT('Role ', rname, ' is not defined')));
  END IF;

  RETURN ok(_role_is_default(@rname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routines_are` (`sname` VARCHAR(64), `rtype` VARCHAR(9), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`',routine_name,'`')
               FROM `information_schema`.`routines`
	       WHERE `routine_schema` = sname
	       AND `routine_type` = rtype);

  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname),
      ' should have the correct ', LOWER(rtype), 's');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are(CONCAT(rtype, 's'), @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routine_has_sql_mode` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(64), `smode` VARCHAR(8192), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN

  
  
  
  DECLARE valid ENUM('REAL_AS_FLOAT','PIPES_AS_CONCAT','ANSI_QUOTES','IGNORE_SPACE',
      'NOT_USED','ONLY_FULL_GROUP_BY','NO_UNSIGNED_SUBTRACTION','NO_DIR_IN_CREATE',
      'POSTGRESQL','ORACLE','MSSQL','DB2','MAXDB','NO_KEY_OPTIONS','NO_TABLE_OPTIONS',
      'NO_FIELD_OPTIONS','MYSQL323','MYSQL40','ANSI','NO_AUTO_VALUE_ON_ZERO','NO_BACKSLASH_ESCAPES',
      'STRICT_TRANS_TABLES','STRICT_ALL_TABLES','NO_ZERO_IN_DATE','NO_ZERO_DATE','INVALID_DATES',
      'ERROR_FOR_DIVISION_BY_ZERO','TRADITIONAL','NO_AUTO_CREATE_USER','HIGH_NOT_PRECEDENCE',
      'NO_ENGINE_SUBSTITUTION','PAD_CHAR_TO_FULL_LENGTH');

  DECLARE EXIT HANDLER FOR 1265 
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag(CONCAT('SQL Mode ', quote_ident(smode), ' is invalid')));

  IF description = '' THEN
    SET description = CONCAT(UPPER(rtype), ' ', quote_ident(sname), '.', quote_ident(rname),
      ' requires SQL Mode ', quote_ident(smode));
  END IF;

  SET valid = smode;

  IF NOT _has_routine(sname, rname, 'FUNCTION') THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT(UPPER(rtype),' ', quote_ident(sname), '.', quote_ident(rname), ' does not exist')));
  END IF;

  RETURN ok(_routine_has_sql_mode(sname, rname, rtype, smode), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routine_is_deterministic` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `val` VARCHAR(3), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT(rtype, ' ', quote_ident(sname), '.', quote_ident(rname),
      ' should have IS_DETERMINISTIC ', quote_ident(val));
  END IF;

  IF val NOT IN('YES','NO') THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag('Is Deterministic must be { YES | NO }'));
  END IF;

  IF NOT _has_routine(sname, rname, rtype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT(ucf(rtype), ' ', quote_ident(sname), '.', quote_ident(rname),
        ' does not exist')));
  END IF;

  RETURN eq(_routine_is_deterministic(sname, rname, rtype), val, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routine_privileges_are` (`sname` VARCHAR(64), `rtype` VARCHAR(9), `rname` VARCHAR(64), `gtee` VARCHAR(81), `ptypes` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
 
  SET @gtee = _format_user(gtee);
  SET @want = ptypes;
  SET @have = _routine_privileges(sname, rtype, rname, @gtee);

  IF description = '' THEN
    SET description = CONCAT('Account ', gtee, ' should have the correct routine privileges');
  END IF;

  IF NOT _has_routine(sname, rname, rtype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT(rtype, ' `', sname, '`.`', rname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('Routine Privileges', @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routine_security_type_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `stype` VARCHAR(7), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT(rtype, ' ', quote_ident(sname), '.', quote_ident(rname),
      ' should have Security Type ' , quote_ident(stype));
  END IF;

  IF stype NOT IN('INVOKER','DEFINER') THEN
    RETURN CONCAT(ok(FALSE, description),'\n',
      diag('Security Type must be { INVOKER | DEFINER }'));
  END IF;

  IF NOT _has_routine(sname, rname, rtype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT(ucf(rtype), ' ', quote_ident(sname), '.', quote_ident(rname), ' does not exist')));
  END IF;

  RETURN eq(_routine_security_type(sname, rname, rtype), stype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routine_sha1_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `sha1` VARCHAR(40), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT(ucf(rtype), ' ', quote_ident(sname), '.', quote_ident(rname),
      ' definition should match expected value');
  END IF;

  IF NOT _has_routine(sname, rname, rtype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT(ucf(rtype), ' ', quote_ident(sname), '.', quote_ident(rname), ' does not exist')));
  END IF;

  
  RETURN eq(LEFT(_routine_sha1(sname, rname, rtype), LENGTH(sha1)), sha1, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `routine_sql_data_access_is` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `sda` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT(rtype ,' ', quote_ident(sname), '.', quote_ident(rname),
      ' should have SQL Data Access ', quote_ident(sda));
  END IF;

  IF NOT rtype IN('FUNCTION','PROCEDURE') THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag('Routine Type must be { FUNCTION | PROCEDURE }'));
  END IF;

  IF NOT sda IN('CONTAINS SQL','NO SQL','READS SQL DATA','MODIFIES SQL DATA') THEN
    RETURN CONCAT(ok(FALSE,description), '\n',
      diag('SQL Data Access must be { CONTAINS SQL | NO SQL | READS SQL DATA | MODIFIES SQL DATA }'));
  END IF;

  IF NOT _has_routine(sname, rname, rtype) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT(ucf(rtype), ' ', quote_ident(sname), '.', quote_ident(rname), ' does not exist')));
  END IF;

  RETURN eq(_routine_sql_data_access(sname, rname, rtype), sda, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `scheduler_is` (`want` VARCHAR(3), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = 'Event scheduler process should be correctly set';
  END IF;

  RETURN eq(_scheduler(), want, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `schemas_are` (`want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`', `schema_name` ,'`')
               FROM `information_schema`.`schemata`);
	  
  IF description = '' THEN
    SET description = 'The correct Schemas should be defined';
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('schemas', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `schema_character_set_is` (`sname` VARCHAR(64), `cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN schema_charset_is(sname, cname, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `schema_charset_is` (`sname` VARCHAR(64), `cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname),
      ' should use Character Set ',  quote_ident(cname));
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
  END IF;

  IF NOT _has_charset(cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Character Set ', quote_ident(cname), ' is not available')));
  END IF;

  RETURN eq(_schema_charset_is(sname), cname, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `schema_collation_is` (`sname` VARCHAR(64), `cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname), ' should have Collation ',  qv(cname));
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
  END IF;

  IF NOT _has_collation(cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Collation ', quote_ident(cname), ' is not available')));
  END IF;

  RETURN eq(_schema_collation_is(sname), cname , description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `schema_privileges_are` (`sname` VARCHAR(64), `gtee` VARCHAR(81), `ptypes` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
 
  SET @gtee = _format_user(gtee);
  SET @want = ptypes;
  SET @have = _schema_privileges(sname, @gtee);

  IF description = '' THEN
    SET description = CONCAT('Account ', gtee, ' should have the correct schema privileges');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', sname, ' does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('Schema Privileges', @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `single_schema_privileges` (`sname` VARCHAR(64), `gtee` VARCHAR(81), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should have privileges on a single schema');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema `', sname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  RETURN ok(_single_schema_priv(sname, @gtee), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `single_table_privileges` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  
  SET @gtee = _format_user(gtee);

  IF description = '' THEN
    SET description = concat('Account ', gtee, ' should have privileges on a single table');
  END IF;

  IF NOT _has_table(sname,tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table `', sname, '`.`', tname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  RETURN ok(_single_table_priv(sname, tname, @gtee), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `skip` (`how_many` INT, `why` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    DECLARE tap TEXT DEFAULT '';
    REPEAT
        SET tap = concat(
            tap,
            CASE WHEN tap = '' THEN '' ELSE '\n' END,
            ok(1, concat('SKIP: ', COALESCE(why, '')))
        );
        SET how_many = how_many - 1;
    UNTIL how_many = 0 END REPEAT;
    RETURN tap;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `subpartition_expression_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64), `expr` LONGTEXT, `description` TEXT) RETURNS LONGTEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Subpartition ', quote_ident(tname), '.', quote_ident(subp),
      ' should have Subpartition Expression ', qv(TRIM(expr)));
  END IF;

  IF NOT _has_subpartition(sname, tname, subp) THEN
    RETURN CONCAT(ok( FALSE, description), '\n',
      diag(CONCAT('Subpartition ', quote_ident(tname), '.', quote_ident(subp),
        ' does not exist')));
  END IF;

  RETURN eq(_subpartition_expression(sname, tname, subp), TRIM(expr), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `subpartition_method_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64), `smeth` VARCHAR(18), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE valid ENUM('HASH', 'LINEAR HASH', 'KEY', 'LINEAR KEY');
  
  DECLARE EXIT HANDLER FOR 1265
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag('Subpartition Method must be { HASH | LINEAR HASH | KEY | LINEAR KEY }'));

  IF description = '' THEN
    SET description = CONCAT('Subpartition ', quote_ident(tname), '.', quote_ident(subp),
      ' should have SubPartition Method ', qv(smeth));
  END IF;

  SET valid = smeth;

  IF NOT _has_subpartition(sname, tname, subp) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Subpartition ', quote_ident(tname),'.', quote_ident(subp),
        ' does not exist')));
  END IF;

  RETURN eq(_subpartition_method(sname, tname, subp), smeth, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `tables_are` (`sname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`',table_name,'`')
               FROM `information_schema`.`tables`
	       WHERE `table_schema` = sname
	       AND `table_type` = 'BASE TABLE');

  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname),
      ' should have the correct Tables');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('tables', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `table_character_set_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.',
      quote_ident(tname), ' should have Character Set ',  qv(cname));
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  IF NOT _has_charset(cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Character Set ', quote_ident(cname), ' is not available')));
  END IF;

  RETURN eq(_table_character_set(sname, tname), cname, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `table_collation_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Table ', quote_ident(sname), '.',
      quote_ident(tname), ' should have Collation ',  qv(cname));
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.',
        quote_ident(tname), ' does not exist')));
  END IF;

  IF NOT _has_collation(cname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Collation ', quote_ident(cname), ' is not available')));
  END IF;

  RETURN eq(_table_collation(sname, tname), cname, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `table_engine_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `ename` VARCHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Table ', quote_ident(sname), '.',
      quote_ident(tname), ' should have Storage Engine ',  quote_ident(ename));
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname), ' does not exist')));
  END IF;

  IF NOT _has_engine(ename) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Storage Engine ', quote_ident(ename), ' is not available')));
  END IF;

  RETURN eq(_table_engine(sname, tname), ename , description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `table_privileges_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81), `ptypes` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
 
  SET @gtee = _format_user(gtee);
  SET @want = ptypes;
  SET @have = _table_privileges(sname, tname, @gtee);

  IF description = '' THEN
    SET description = CONCAT('Account ', gtee, ' should have the correct table privileges');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table `', sname,'`.`', tname, '` does not exist')));
  END IF;

  IF NOT _has_user_at_host(@gtee) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Account ', gtee, ' does not exist')));
  END IF;

  

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('Table Privileges', @extras, @missing, description);

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `table_sha1_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `sha1` VARCHAR(40), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' definition should match expected value');
  END IF;

  IF NOT _has_table(sname, tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname), ' does not exist')));
  END IF;

  
  RETURN eq(LEFT(_table_sha1(sname, tname), LENGTH(sha1)), sha1, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `timezones_updated` (`description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Timezones data should be updated for changes');
  END IF; 

  IF NOT _has_timezones() THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table `mysql`.`time_zone_data` is empty')));
  END IF;

  RETURN ok(_timezones_updated(), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `triggers_are` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`', `trigger_name` ,'`')
               FROM `information_schema`.`triggers`
               WHERE `trigger_schema` = sname
               AND `event_object_table` = tname);
	  
  IF description = '' THEN 
     SET description = CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
      ' should have the correct Triggers');
  END IF;

  IF NOT _has_table(sname,tname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Table ', quote_ident(sname), '.', quote_ident(tname),
        ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('triggers', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `trigger_event_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64), `evnt` VARCHAR(6), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = concat('Trigger ', quote_ident(tname), '.', quote_ident(trgr),
      ' Event should occur for ', qv(UPPER(evnt)));
  END IF;

  IF NOT _has_trigger(sname, tname, trgr) THEN
    RETURN CONCAT(ok( FALSE, description), '\n',
      diag(CONCAT('Trigger ', quote_ident(tname),'.', quote_ident(trgr),
        ' does not exist')));
  END IF;

  RETURN eq(_trigger_event(sname, tname, trgr), evnt, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `trigger_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64), `act_state` LONGTEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Trigger ', quote_ident(tname), '.', quote_ident(trgr), 
      ' should have the correct action');
  END IF;

  IF NOT _has_trigger(sname, tname, trgr) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Trigger ', quote_ident(tname),'.', quote_ident(trgr),
        ' does not exist')));
  END IF;

  RETURN eq(_trigger_is(sname, tname, trgr), act_state, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `trigger_order_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64), `seq` BIGINT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Trigger ', quote_ident(tname), '.', quote_ident(trgr),
      ' should have Action Order ', qv(seq));
  END IF;

  IF NOT _has_trigger(sname, tname, trgr) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Trigger ', quote_ident(tname),'.', quote_ident(trgr),
        ' does not exist')));
  END IF;

  RETURN eq(_trigger_order(sname, tname, trgr), seq, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `trigger_timing_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64), `timing` VARCHAR(6), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('Trigger ', quote_ident(tname), '.', quote_ident(trgr),
      ' should have Timing ', qv(UPPER(timing)));
  END IF;

  IF NOT _has_trigger(sname, tname, trgr) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Trigger ', quote_ident(tname),'.', quote_ident(trgr),
        ' does not exist')));
  END IF;

  RETURN eq(_trigger_timing(sname, tname, trgr), timing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `ucf` (`val` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  RETURN CONCAT(UPPER(LEFT(val, 1)), LOWER(SUBSTRING(val, 2)));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `unalike` (`got` TEXT, `pat` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN _unalike( got NOT LIKE pat, got, pat, descr );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `uqi` (`ident` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN


  IF LEFT(ident,1) = '`' AND RIGHT(ident,1) = '`' THEN
	  RETURN TRIM(BOTH '`' FROM REPLACE(ident,'``','`'));
  END IF;

  IF LEFT(ident,1) = '"' AND RIGHT(ident,1) = '"' THEN
	  RETURN TRIM(BOTH '"' FROM REPLACE(ident,'""','"'));
  END IF;

  RETURN ident;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `user_hasnt_lifetime` (`hname` CHAR(60), `uname` CHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('User \'', uname, '\'@\'', hname, '\' Password should not expire');
  END IF;

  IF NOT _has_user(hname, uname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('User \'', uname, '\'@\'', hname, '\' does not exist')));
  END IF;

  RETURN ok(NOT _user_has_lifetime(hname, uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `user_has_lifetime` (`hname` CHAR(60), `uname` CHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('User \'', uname, '\'@\'', hname, '\' Password should expire');
  END IF;

  IF NOT _has_user(hname, uname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('User \'', uname, '\'@\'', hname, '\' does not exist')));
  END IF;

  RETURN ok(_user_has_lifetime(hname, uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `user_not_ok` (`hname` CHAR(60), `uname` CHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('User \'', uname, '\'@\'',
      hname, '\' should be locked out or have an expired password');
  END IF;

  IF NOT _has_user(hname, uname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('User \'', uname, '\'@\'', hname, '\' does not exist')));
  END IF;

  RETURN ok(NOT _user_ok( hname, uname ), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `user_ok` (`hname` CHAR(60), `uname` CHAR(32), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('User \'', uname, '\'@\'',
      hname, '\' should not be locked or have expired password');
  END IF;

  IF NOT _has_user(hname, uname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('User \'', uname, '\'@\'', hname, '\' does not exist')));
  END IF;

  RETURN ok(_user_ok(hname, uname), description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `views_are` (`sname` VARCHAR(64), `want` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  SET @want = want;
  SET @have = (SELECT GROUP_CONCAT('`',table_name,'`')
               FROM `information_schema`.`tables`
	       WHERE `table_schema` = sname
	       AND `table_type` = 'VIEW');

  IF description = '' THEN
    SET description = CONCAT('Schema ', quote_ident(sname),
      ' should have the correct Views');
  END IF;

  IF NOT _has_schema(sname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('Schema ', quote_ident(sname), ' does not exist')));
  END IF;

  CALL _populate_want(@want);
  CALL _populate_have(@have);

  SET @missing = (SELECT _missing(@have)); 
  SET @extras  = (SELECT _extra(@want));

  RETURN _are('views', @extras, @missing, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `view_check_option_is` (`sname` VARCHAR(64), `vname` VARCHAR(64), `copt` VARCHAR(8), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
      ' should have Check Option ', qv(copt));
  END IF;

  IF NOT _has_view(sname, vname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
        ' does not exist')));
  END IF;

  RETURN eq(_view_check_option(sname, vname), copt, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `view_definer_is` (`sname` VARCHAR(64), `vname` VARCHAR(64), `dfnr` VARCHAR(93), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ',
       quote_ident(sname), '.', quote_ident(vname), ' should have Definer ', qv(dfnr));
  END IF;

  IF NOT _has_view(sname, vname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
        ' does not exist')));
  END IF;

  RETURN eq(_view_definer(sname, vname), dfnr, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `view_is_updatable` (`sname` VARCHAR(64), `vname` VARCHAR(64), `updl` VARCHAR(3), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
    ' should have Is Updatable ', qv(updl));
  END IF;

  IF NOT _has_view(sname, vname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
        ' does not exist')));
  END IF;

  RETURN eq(_view_is_updatable(sname, vname), updl, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `view_security_type_is` (`sname` VARCHAR(64), `vname` VARCHAR(64), `stype` VARCHAR(7), `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  IF description = '' THEN
    SET description = CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
       ' should have Security Type ', qv(stype));
  END IF;

  IF NOT _has_view(sname, vname) THEN
    RETURN CONCAT(ok(FALSE, description), '\n',
      diag(CONCAT('View ', quote_ident(sname), '.', quote_ident(vname),
        ' does not exist')));
  END IF;

  RETURN eq(_view_security_type( sname, vname), stype, description);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_add` (`vlabel` TEXT, `vvalue` INTEGER, `vnote` TEXT) RETURNS INT(11) MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    INSERT INTO __tcache__ (label, cid, value, note)
    VALUES (vlabel, connection_id(), vvalue, COALESCE(vnote, ''));
    RETURN vvalue;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_alike` (`res` BOOLEAN, `got` TEXT, `pat` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF res THEN RETURN  ok( res, descr ); END IF;
    RETURN concat(ok(res, descr), '\n',  diag(concat(
           '                  ', COALESCE( quote(got), 'NULL' ),
        '\n   doesn''t match: ', COALESCE( quote(pat), 'NULL' )
    )));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_are` (`what` TEXT, `extras` TEXT, `missing` TEXT, `description` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
DECLARE msg TEXT    DEFAULT '';
DECLARE res BOOLEAN DEFAULT TRUE;

  IF extras <> '' THEN
    SET res = FALSE;
    SET msg = CONCAT('\n', CONCAT('\n'
      '    Extra ', what, ':\n       ' , REPLACE( extras, ',', '\n       ')));
  END IF;

  IF missing <> '' THEN
    SET res = FALSE;
    SET msg = CONCAT(msg, CONCAT('\n'
      '    Missing ', what, ':\n       ' , REPLACE( missing, ',', '\n       ')));
  END IF;

  RETURN CONCAT(ok(res, description), diag(msg));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_column_privileges` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `gtee` VARCHAR(81)) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE rtn TEXT;
   
  IF @rollup = 1 THEN
    SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
    FROM
      ( SELECT `privilege_type`
        FROM `information_schema`.`user_privileges`
        WHERE `grantee` = gtee AND _column_privs(`privilege_type`) > 0 
      UNION
        SELECT `privilege_type`
        FROM `information_schema`.`schema_privileges`
        WHERE `grantee` = gtee AND `table_schema` = sname AND _column_privs (`privilege_type`) > 0
      UNION
        SELECT `privilege_type`
        FROM `information_schema`.`table_privileges`
        WHERE `grantee` = gtee AND `table_schema` = sname AND `table_name` = tname AND _column_privs (`privilege_type`) > 0
      UNION
        SELECT `privilege_type`
        FROM `information_schema`.`column_privileges`
        WHERE `grantee` = gtee AND `table_schema` = sname AND `table_name` = tname AND `column_name` = cname
      ) u;
   ELSE
     SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
     FROM `information_schema`.`column_privileges`
     WHERE `grantee` = gtee AND `table_schema` = sname AND `table_name` = tname AND `column_name` = cname;
  END IF;

  RETURN rtn;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_column_privs` (`ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  RETURN FIND_IN_SET(ptype, 'INSERT,REFERENCES,SELECT,UPDATE');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_column_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS LONGTEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret LONGTEXT;

  SELECT `column_type` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_charset` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret VARCHAR(32);

  SELECT `character_set_name` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_collation` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret VARCHAR(32);

  SELECT `collation_name` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_default` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS LONGTEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret LONGTEXT;

  SELECT `column_default` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN ret ;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_extra_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(30) CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret VARCHAR(30);

  SELECT `extra` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_default` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND CASE tap.mysql_variant()
    WHEN 'MariaDB' THEN `column_default` <> 'NULL'
    ELSE `column_default` IS NOT NULL
  END;

  RETURN coalesce(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_index_key` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `index_name` <> 'PRIMARY'
  LIMIT 1;

  RETURN coalesce(ret, false);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_named_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `kname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `index_name` = kname;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_non_unique_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT true into ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `index_name` <> 'PRIMARY'
  AND `non_unique` = 1
  limit 1; 

  RETURN coalesce(ret, false);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_pos_in_named_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `kname` VARCHAR(64), `pos` INT) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `index_name` = kname
  AND `seq_in_index` = pos;

  RETURN coalesce(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_primary_key` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `column_key` = 'PRI';

  RETURN coalesce(ret, false);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `ctype` LONGTEXT) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `column_type` = ctype;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_has_unique_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT true into ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname
  AND `index_name` <> 'PRIMARY'
  AND `non_unique` = 0
  limit 1; 

  RETURN coalesce(ret, false);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_is_pk` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT COUNT(`indexdef`) INTO ret
  FROM 
    (
      SELECT `table_name`, `index_name`,
      GROUP_CONCAT(CONCAT('`', `column_name`, '`') ORDER BY `seq_in_index`) AS `indexdef`
      FROM `information_schema`.`statistics`
      WHERE `table_schema` = sname
      AND `table_name` = tname
      GROUP BY `table_name`,`index_name`
    ) indices
  WHERE `index_name` = 'PRIMARY'
  AND `indexdef` = want;

  RETURN IF(ret <> 0 , TRUE, FALSE);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_is_unique` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT COUNT(`indexdef`) INTO ret
  FROM
    (
      SELECT `table_name`, `index_name`,
      GROUP_CONCAT(CONCAT('`', `column_name`, '`') ORDER BY `seq_in_index`) AS `indexdef`
      FROM `information_schema`.`statistics`
      WHERE `table_schema` = sname
      AND `table_name` = tname
      AND `non_unique` = 0
      GROUP BY `table_name`,`index_name`
     ) indices 
  WHERE `indexdef` = want;

  RETURN IF(ret > 0 , 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_col_nullable` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(3) CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret VARCHAR(3);

  SELECT `is_nullable` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_constraint_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(64) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(64);

  SELECT `constraint_type` INTO ret
  FROM `information_schema`.`table_constraints`
  WHERE `constraint_schema` = sname
  AND `table_name` = tname
  AND `constraint_name` = cname;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_datatype` (`word` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN

  SET word =
    CASE
      WHEN word IN ('BOOL', 'BOOLEAN') THEN 'TINYINT'
      WHEN word =  'INTEGER' THEN 'INT'
      WHEN word IN ('DEC', 'NUMERIC', 'FIXED') THEN 'DECIMAL'
      WHEN word IN ('DOUBLE_PRECISION') THEN 'DOUBLE'
      WHEN word = 'REAL' THEN IF (INSTR(@@GLOBAL.sql_mode, 'REAL_AS_FLOAT') > 0 , 'FLOAT' , 'DOUBLE')
      WHEN word IN ('NCHAR', 'CHARACTER', 'NATIONAL_CHARACTER') THEN 'CHAR'
      WHEN word IN ('NVARCHAR', 'VARCHARACTER', 'CHARACTER_VARYING', 'NATIONAL_VARCHAR') THEN 'VARCHAR'
      WHEN word = 'CHAR_BYTE' THEN 'BIT'
      ELSE word
	END ;

  RETURN word;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_data_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS LONGTEXT CHARSET utf8mb4 READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret LONGTEXT;

  SELECT `data_type` INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_engine_default` () RETURNS VARCHAR(64) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(64);

  SELECT `engine` INTO ret
  FROM `information_schema`.`engines`
  WHERE `support` = 'DEFAULT';

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_eq` (`have` TEXT, `want` TEXT) RETURNS TINYINT(1) BEGIN
    RETURN (have IS NOT NULL AND want IS NOT NULL AND have = want)
        OR (have IS NULL AND want IS NULL)
        OR 0;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_event_interval_field` (`sname` VARCHAR(64), `ename` VARCHAR(64)) RETURNS VARCHAR(18) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(18);

  SELECT `interval_field` INTO ret
  FROM `information_schema`.`events`
  WHERE `event_schema` = sname
  AND `event_name` = ename;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_event_interval_value` (`sname` VARCHAR(64), `ename` VARCHAR(64)) RETURNS VARCHAR(256) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(256);

  SELECT `interval_value` INTO ret
  FROM `information_schema`.`events`
  WHERE `event_schema` = sname
  AND `event_name` = ename;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_event_status` (`sname` VARCHAR(64), `ename` VARCHAR(64)) RETURNS VARCHAR(18) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(18);

  SELECT `status` INTO ret
  FROM `information_schema`.`events`
  WHERE `event_schema` = sname
  AND `event_name` = ename;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_event_type` (`sname` VARCHAR(64), `ename` VARCHAR(64)) RETURNS VARCHAR(9) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(9);

  SELECT `event_type` INTO ret
  FROM `information_schema`.`events`
  WHERE `event_schema` = sname
  AND `event_name` = ename;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_extra` (`want` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE ret TEXT;

  SET @want = REPLACE(want,'`','');

  SELECT GROUP_CONCAT(qi(`ident`)) INTO ret
  FROM `have`
  WHERE NOT COALESCE(FIND_IN_SET(`ident`, @want),0);

  RETURN COALESCE(ret, '');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_finish` (`curr_test` INTEGER, `exp_tests` INTEGER, `num_faild` INTEGER) RETURNS TEXT CHARSET utf8mb4 MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE ret    TEXT DEFAULT '';
    DECLARE plural CHAR DEFAULT '';
    IF exp_tests = 1 THEN SET plural = 's'; END IF;

    IF curr_test IS NULL THEN
        CALL _cleanup();
        
        SELECT `# No tests run!` INTO ret;
    END IF;

    IF exp_tests = 0 OR exp_tests IS NULL THEN
         
        SET exp_tests = curr_test;
        SET ret = concat('1..', COALESCE(exp_tests, 0));
    END IF;

    IF curr_test <> exp_tests THEN
        SET ret = concat(ret, CASE WHEN ret THEN '\n' ELSE '' END, diag(concat(
            'Looks like you planned ', exp_tests, ' test',
            plural, ' but ran ', curr_test
        )));
    ELSEIF num_faild > 0 THEN
        SET ret = concat(ret, CASE WHEN ret THEN '\n' ELSE '' END, diag(concat(
            'Looks like you failed ', num_faild, ' test',
            CASE num_faild WHEN 1 THEN '' ELSE 's' END,
            ' of ', exp_tests
        )));
    END IF;

    
    CALL _cleanup();
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_fixCSL` (`want` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN

	SET want = REPLACE(want, '''','');
	SET want = REPLACE(want, '"','');
	SET want = REPLACE(want, '\n','');






	RETURN want;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_fk_ok` (`csch` VARCHAR(64), `ctab` VARCHAR(64), `ccol` TEXT, `usch` VARCHAR(64), `utab` VARCHAR(64), `ucol` TEXT) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT COUNT(*) INTO ret
  FROM 
    (
      SELECT kc.`constraint_schema` AS `csch`,
             kc.`table_name` AS `ctab`,
             GROUP_CONCAT(CONCAT('`',kc.`column_name`,'`') ORDER BY kc.`ordinal_position`) AS `cols1`,
             kc.`referenced_table_schema` AS `usch`,
             kc.`referenced_table_name` AS `utab`,
             GROUP_CONCAT(CONCAT('`',kc.`referenced_column_name`,'`') ORDER BY `position_in_unique_constraint`) AS `cols2`
      FROM `information_schema`.`key_column_usage` kc 
      WHERE kc.`constraint_schema` = csch AND kc.`referenced_table_schema` = usch
      AND kc.`table_name` = ctab AND kc.`referenced_table_name` = utab
      GROUP BY 1,2,4,5
      HAVING GROUP_CONCAT(CONCAT('`',kc.`column_name`,'`') ORDER BY kc.`ordinal_position`) = ccol
         AND GROUP_CONCAT(CONCAT('`',kc.`referenced_column_name`,'`') ORDER BY `position_in_unique_constraint`) = ucol
    ) fkey;

  RETURN COALESCE(ret,0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_fk_on_delete` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(64) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(64);

  SELECT `delete_rule` INTO ret
  FROM `information_schema`.`referential_constraints`
  WHERE `constraint_schema` = sname
  AND `table_name` = tname
  AND `constraint_name` = cname;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_fk_on_update` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS VARCHAR(64) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(64);

  SELECT `update_rule` INTO ret
  FROM `information_schema`.`referential_constraints`
  WHERE `constraint_schema` = sname
  AND `table_name` = tname
  AND `constraint_name` = cname;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_format_user` (`uname` CHAR(97)) RETURNS CHAR(97) CHARSET utf8mb4 BEGIN

  SET @uname = uname;
  SET @uname = REPLACE(@uname, '"','''');
  SET @uname = REPLACE(@uname, '`','''');

  IF @uname REGEXP '@' = 0 THEN
    SET @uname = CONCAT(@uname, '@\'%\'');
  END IF;

  IF LEFT(@uname,1) != '''' THEN
    SET @uname = CONCAT('''', @uname);
  END IF;

  IF LOCATE('''@', @uname) = 0 THEN
    SET @uname = REPLACE(@uname, '@', '''@');
  END IF;

  IF LOCATE('@''', @uname) = 0 THEN
    SET @uname = REPLACE(@uname, '@', '@''');
  END IF;

  IF RIGHT(@uname,1) != '''' THEN
    SET @uname = CONCAT(@uname,'''');
  END IF;

  RETURN @uname;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_function_data_type` (`sname` VARCHAR(64), `rname` VARCHAR(64)) RETURNS VARCHAR(64) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(64);

  SELECT `data_type` INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname
  AND `routine_type` = 'FUNCTION';

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_get` (`vlabel` TEXT) RETURNS INT(11) READS SQL DATA
BEGIN
    DECLARE ret integer;
    SELECT value INTO ret
      FROM __tcache__
     WHERE cid   = connection_id()
       AND label = vlabel LIMIT 1;
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_get_latest_id` (`vlabel` TEXT) RETURNS INT(11) READS SQL DATA
BEGIN
    DECLARE ret integer;
    SELECT id INTO ret
      FROM __tcache__
     WHERE cid = connection_id()
       AND label = vlabel
       AND id = (SELECT MAX(id) FROM __tcache__ WHERE cid = connection_id() AND label = vlabel)
     LIMIT 1;
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_get_latest_value` (`vlabel` TEXT) RETURNS INT(11) READS SQL DATA
BEGIN
    DECLARE ret integer;
    SELECT value INTO ret
      FROM __tcache__
     WHERE cid = connection_id()
       AND label = vlabel
       AND id = (SELECT MAX(id) FROM __tcache__ WHERE cid = connection_id() AND label = vlabel)
     LIMIT 1;
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_get_latest_with_value` (`vlabel` TEXT, `vvalue` INTEGER) RETURNS INT(11) READS SQL DATA
BEGIN
    DECLARE ret integer;
    SELECT MAX(id)
      INTO ret
      FROM __tcache__
     WHERE label = vlabel
       AND value = vvalue
       AND cid = connection_id();
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_get_note_by_id` (`vid` INTEGER) RETURNS TEXT CHARSET utf8mb4 READS SQL DATA
BEGIN
    DECLARE ret TEXT;
    SELECT note INTO ret FROM __tcache__ WHERE id = vid  LIMIT 1;
    RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_global_privileges` (`gtee` VARCHAR(81)) RETURNS TEXT CHARSET utf8mb4 BEGIN
   DECLARE rtn TEXT;
   SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
   FROM `information_schema`.`user_privileges`
   WHERE `grantee` = gtee;

   RETURN rtn;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_global_privs` (`ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  RETURN FIND_IN_SET(ptype,
  'ALTER,ALTER ROUTINE,CREATE,CREATE ROUTINE,CREATE TABLESPACE,CREATE TEMPORARY TABLES,CREATE USER,CREATE VIEW,DELETE,DROP,EVENT,EXECUTE,FILE,GRANT,INDEX,INSERT,LOCK TABLES,PROCESS,REFERENCES,RELOAD,REPLICATION CLIENT,REPLICATION SLAVE,SELECT,SHOW DATABASES,SHOW VIEW,SHUTDOWN,SUPER,TRIGGER,UPDATE,USAGE');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_global_var` (`var` VARCHAR(64)) RETURNS VARCHAR(1024) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(1024);

  SELECT `variable_value` INTO ret
  FROM `performance_schema`.`global_variables`
  WHERE `variable_name` = var;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_charset` (`cname` VARCHAR(32)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`character_sets`
  WHERE `character_set_name` = cname;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_collation` (`cname` VARCHAR(32)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`collations`
  WHERE `collation_name` = cname
  AND `is_compiled` = 'YES';

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_column` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) READS SQL DATA
    DETERMINISTIC
BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`columns`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `column_name` = cname;

  RETURN coalesce(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_column_priv` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT;

  IF @rollup = 1 THEN
    SELECT SUM(priv) INTO rtn
    FROM
    (
      SELECT 1 AS priv
      FROM `information_schema`.`user_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
    UNION ALL
      SELECT 1 AS priv
      FROM `information_schema`.`schema_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
      AND `table_schema` = sname
   UNION ALL
     SELECT 1 AS priv
     FROM `information_schema`.`table_privileges`
     WHERE `grantee` = gtee
     AND `privilege_type` = ptype
     AND `table_schema` = sname
     AND `table_name` = tname
   UNION ALL
     SELECT 1 AS priv
     FROM `information_schema`.`column_privileges`
     WHERE `grantee` = gtee
     AND `privilege_type` = ptype
     AND `table_schema` = sname
     AND `table_name` = tname
     AND `column_name` = cname
   ) a;
  ELSE
    SELECT 1 INTO rtn
    FROM `information_schema`.`column_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
    AND `table_schema` = sname
    AND `table_name` = tname
    AND `column_name` = cname;
  END IF;

  RETURN IF(rtn > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_constraint` (`sname` VARCHAR(64), `tname` VARCHAR(64), `cname` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret INT;

  SELECT COUNT(*) INTO ret
  FROM `information_schema`.`table_constraints`
  WHERE `constraint_schema` = sname
  AND `table_name` = tname
  AND `constraint_name` = cname;

  RETURN IF(ret > 0 , 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_constraint_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `ctype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret INT;

  SELECT COUNT(*) INTO ret
  FROM `information_schema`.`table_constraints`
  WHERE `constraint_schema` = sname
  AND `table_name` = tname
  AND `constraint_type` = ctype;

  RETURN IF(ret > 0 , 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_engine` (`ename` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`engines`
  WHERE `engine` = ename
  AND (`support` = 'YES' OR `support` = 'DEFAULT');

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_event` (`sname` VARCHAR(64), `ename` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`events`
  WHERE `event_schema` = sname
  AND `event_name` = ename;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_global_priv` (`gtee` VARCHAR(81), `ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT DEFAULT 0;

  SELECT 1 INTO rtn
  FROM `information_schema`.`user_privileges`
  WHERE `grantee` = gtee
  AND `privilege_type` = ptype;

  RETURN rtn;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_index` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `index_name` = iname
  LIMIT 1; 

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_partition` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `partition_name` = part
  LIMIT 1;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_partitioning` () RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`plugins`
  WHERE `plugin_type`='STORAGE ENGINE'
  AND `plugin_name` = 'partition'
  AND `plugin_status` = 'active';

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_priv` (`gtee` VARCHAR(81), `ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT;
  SELECT SUM(priv) INTO rtn
  FROM
  ( 
    SELECT 1 AS priv
    FROM `information_schema`.`user_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
  UNION ALL
    SELECT 1 AS priv
    FROM `information_schema`.`schema_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
  UNION ALL
    SELECT 1 AS priv
    FROM `information_schema`.`table_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
  UNION ALL
    SELECT 1 AS priv
    FROM `information_schema`.`column_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
  ) a;

  RETURN IF(rtn > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_role` (`rname` CHAR(93)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT COUNT(*) INTO ret
  FROM `mysql`.`role_edges`
  WHERE CONCAT('''', `from_user`, '''@''', from_host, '''') = rname;

  RETURN IF(ret > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_routine` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname
  AND `routine_type` = rtype;

  RETURN COALESCE(ret,0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_schema` (`sname` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`schemata`
  WHERE `schema_name` = sname;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_schema_priv` (`sname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT;

  IF @rollup = 1 THEN
    SELECT SUM(priv) INTO rtn
    FROM
    (
      SELECT 1 AS priv
      FROM `information_schema`.`user_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
    UNION ALL
      SELECT 1 AS priv
      FROM `information_schema`.`schema_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
      AND `table_schema` = sname
    ) a;
  ELSE
    SELECT 1 INTO rtn
    FROM `information_schema`.`schema_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
    AND `table_schema` = sname;
  END IF;

  RETURN IF(rtn > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_security` (`sname` VARCHAR(64), `vname` VARCHAR(64), `vsecurity` VARCHAR(9)) RETURNS TINYINT(1) BEGIN
  DECLARE ret boolean;

  SELECT 1 INTO ret
  FROM `information_schema`.`views`
  WHERE `table_schema` = sname
  AND `table_name` = vname
  AND `security_type` = vsecurity;

  RETURN coalesce(ret, false);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_subpartition` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `subpartition_name` = subp;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_table` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`tables`
  WHERE `table_name` = tname
  AND `table_schema` = sname;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_table_priv` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81), `ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT;

  IF @rollup = 1 THEN
    SELECT SUM(priv) INTO rtn
    FROM
    (
      SELECT 1 AS priv
      FROM `information_schema`.`user_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
    UNION ALL
      SELECT 1 AS priv
      FROM `information_schema`.`schema_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
      AND `table_schema` = sname
    UNION ALL
      SELECT 1 AS priv
      FROM `information_schema`.`table_privileges`
      WHERE `grantee` = gtee
      AND `privilege_type` = ptype
      AND `table_schema` = sname
      AND `table_name` = tname
     ) a;
  ELSE
    SELECT 1 INTO rtn
    FROM `information_schema`.`table_privileges`
    WHERE `grantee` = gtee
    AND `privilege_type` = ptype
    AND `table_schema` = sname
    AND `table_name` = tname;
  END IF;
  
  RETURN IF(rtn > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_timezones` () RETURNS TINYINT(1) BEGIN
  DECLARE ret INT;

  SELECT count(*) INTO ret
  FROM `mysql`.`time_zone_name`;

  RETURN IF(ret > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_trigger` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`triggers`
  WHERE `trigger_schema` = sname
  AND `event_object_table` = tname
  AND `trigger_name` = trgr;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_unique` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT COUNT(`table_name`) INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `non_unique` = 0;

  RETURN IF(ret <> 0 , TRUE, FALSE);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_user` (`hname` CHAR(60), `uname` CHAR(32)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `mysql`.`user`
  WHERE `host` = hname
  AND `user` = uname;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_user_at_host` (`uname` CHAR(97)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `mysql`.`user`
  WHERE CONCAT('\'',`user`, '\'@\'', `host`, '\'') = uname;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_has_view` (`sname` VARCHAR(64), `vname` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM `information_schema`.`tables`
  WHERE `table_schema` = sname
  AND `table_name` = vname
  AND `table_type` = 'VIEW';

  RETURN coalesce(ret, false);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_index_def` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64)) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE ret TEXT;

  SELECT GROUP_CONCAT(CONCAT('`', `column_name`, '`'),
  IF(`sub_part` IS NULL, '', CONCAT('(', `sub_part`, ')'))) AS 'column_name' INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `index_name` = iname
  ORDER BY `seq_in_index`;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_index_type` (`sname` VARCHAR(64), `tname` VARCHAR(64), `iname` VARCHAR(64)) RETURNS VARCHAR(16) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(16);

  SELECT `index_type` INTO ret
  FROM `information_schema`.`statistics`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `index_name` = iname
  LIMIT 1; 

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_is_indexed` (`sname` VARCHAR(64), `tname` VARCHAR(64), `want` TEXT) RETURNS TINYINT(1) BEGIN
    DECLARE ret BOOLEAN;

    SELECT COUNT(`indexdef`) INTO ret
    FROM
      (
        SELECT `table_name`, `index_name`,
        GROUP_CONCAT(CONCAT('`', `column_name`, '`') ORDER BY `seq_in_index`) AS `indexdef`
        FROM `information_schema`.`statistics`
        WHERE `table_schema` = sname
        AND `table_name` = tname
        GROUP BY `table_name`,`index_name`
      ) indices
    WHERE `indexdef` = want;

    RETURN IF(ret <> 0 , TRUE, FALSE);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_missing` (`have` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE ret TEXT;

  SET @have = REPLACE(have,'`','');

  SELECT GROUP_CONCAT(qi(`ident`)) INTO ret
  FROM `want`

  WHERE NOT COALESCE(FIND_IN_SET(`ident`, @have),0);

  RETURN COALESCE(ret, '');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_nextnumb` () RETURNS INT(11) BEGIN
    DECLARE nextnumb INTEGER DEFAULT COALESCE(_get('tnumb'), 0) + 1;
    RETURN _set('tnumb', nextnumb, '');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_partition_count` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS SMALLINT(6) BEGIN
  DECLARE ret SMALLINT;

  SELECT COUNT(*) INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `partition_name` IS NOT NULL;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_partition_expression` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64)) RETURNS LONGTEXT CHARSET utf8mb4 BEGIN
  DECLARE ret LONGTEXT;

  SELECT TRIM(`partition_expression`) INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `partition_name` = part
  LIMIT 1;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_partition_method` (`sname` VARCHAR(64), `tname` VARCHAR(64), `part` VARCHAR(64)) RETURNS VARCHAR(18) CHARSET utf8mb4 BEGIN
DECLARE ret VARCHAR(18);

  SELECT `partition_method` INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `partition_name` = part
  LIMIT 1;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_role_is_default` (`rname` CHAR(93)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT COUNT(*) INTO ret
  FROM `mysql`.`default_roles`
  WHERE CONCAT('''', `default_role_user`, '''@''', `default_role_host`, '''') = rname;

  RETURN IF(ret > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_has_sql_mode` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9), `smode` VARCHAR(8192)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT LOCATE(smode, `sql_mode`) INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname
  AND `routine_type` = rtype;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_is_deterministic` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9)) RETURNS VARCHAR(3) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(3);

  SELECT `is_deterministic` INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname
  AND `routine_type` = rtype;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_privileges` (`sname` VARCHAR(64), `rtype` VARCHAR(9), `rname` VARCHAR(64), `gtee` VARCHAR(81)) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE rtn TEXT;

  IF @rollup = 1 THEN
  SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
  FROM
    ( SELECT `privilege_type`
      FROM `information_schema`.`user_privileges`
      WHERE `grantee` = gtee AND _routine_privs(`privilege_type`) > 0   
    UNION
      SELECT `privilege_type`
      FROM `information_schema`.`schema_privileges`
      WHERE `table_schema` = sname AND `grantee` = gtee AND _routine_privs(`privilege_type`) > 0
    UNION
      SELECT `privilege_type`
      FROM `tap`.`proc_privileges`
      WHERE `routine_schema` = sname AND `routine_name` = rname AND `routine_type` = rtype AND `grantee` = gtee
    ) u;
  ELSE
    SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
    FROM `tap`.`proc_privileges`
    WHERE `routine_schema` = sname AND `routine_name` = rname AND `routine_type` = rtype AND `grantee` = gtee;
  END IF;  

  RETURN rtn;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_privs` (`ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  RETURN FIND_IN_SET(ptype, 'ALTER ROUTINE,CREATE ROUTINE,EXECUTE,GRANT');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_security_type` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9)) RETURNS VARCHAR(7) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(7);

  SELECT `security_type` INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname
  AND `routine_type` = rtype ;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_sha1` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9)) RETURNS CHAR(40) CHARSET utf8mb4 BEGIN
  DECLARE ret CHAR(40);

  SELECT SHA1(`routine_definition`) INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_routine_sql_data_access` (`sname` VARCHAR(64), `rname` VARCHAR(64), `rtype` VARCHAR(9)) RETURNS VARCHAR(64) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(64);

  SELECT `sql_data_access` INTO ret
  FROM `information_schema`.`routines`
  WHERE `routine_schema` = sname
  AND `routine_name` = rname
  AND `routine_type` = rtype ;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_scheduler` () RETURNS VARCHAR(3) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(3);
    
  SELECT @@GLOBAL.event_scheduler INTO ret;

  RETURN ret;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_schema_charset_is` (`sname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(32);

  SELECT `default_character_set_name` INTO ret
  FROM `information_schema`.`schemata`
  WHERE `schema_name` = sname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_schema_collation_is` (`sname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(32);

  SELECT `default_collation_name` INTO ret
  FROM `information_schema`.`schemata`
  WHERE `schema_name` = sname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_schema_privileges` (`sname` VARCHAR(64), `gtee` VARCHAR(81)) RETURNS TEXT CHARSET utf8mb4 BEGIN
   DECLARE rtn TEXT;
   
   IF @rollup = 1 THEN
     SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
     FROM
     ( SELECT `privilege_type`
       FROM `information_schema`.`user_privileges`
       WHERE `grantee` = gtee AND _schema_privs(`privilege_type`) > 0 
     UNION 
       SELECT `privilege_type`
       FROM `information_schema`.`schema_privileges`
       WHERE `grantee` = gtee AND `table_schema` = sname
     ) u;
   ELSE
     SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
     FROM `information_schema`.`schema_privileges`
     WHERE `grantee` = gtee AND `table_schema` = sname;
   END IF;
   
   RETURN rtn;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_schema_privs` (`ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  RETURN FIND_IN_SET(ptype,
    'ALTER,ALTER ROUTINE,CREATE,CREATE ROUTINE,CREATE TEMPORARY TABLES,CREATE VIEW,DELETE,DROP,EVENT,EXECUTE,GRANT,INDEX,INSERT,LOCK TABLES,REFERENCES,SELECT,SHOW VIEW,TRIGGER,UPDATE'); 
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_set` (`vlabel` TEXT, `vvalue` INTEGER, `vnote` TEXT) RETURNS INT(11) READS SQL DATA
BEGIN
    UPDATE __tcache__
       SET value = vvalue,
           note  = COALESCE(vnote, '')
     WHERE cid   = connection_id()
       AND label = vlabel;
    IF ROW_COUNT() = 0 THEN
        RETURN _add( vlabel, vvalue, vnote );
    END IF;
    RETURN vvalue;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_single_schema_priv` (`sname` VARCHAR(64), `gtee` VARCHAR(81)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT;

  SELECT COUNT(DISTINCT `table_schema`) INTO rtn
  FROM `information_schema`.`schema_privileges`
  WHERE `grantee` = gtee
  AND `table_schema` = sname
  AND NOT EXISTS (
    SELECT *
    FROM information_schema.schema_privileges
    WHERE `grantee` = gtee
    AND `table_schema` != sname
  )
  AND NOT EXISTS (
    SELECT *
    FROM `information_schema`.`user_privileges`
    WHERE `grantee` = gtee
    AND _schema_privs(`privilege_type`) > 0
  )
  AND NOT EXISTS (
    SELECT *
    FROM `information_schema`.`table_privileges`
    WHERE `grantee` = gtee
    AND `table_schema` != sname
  )
  AND NOT EXISTS (
  SELECT *
  FROM `information_schema`.`column_privileges`
  WHERE `grantee` = gtee
  AND `table_schema` != sname
  )
  AND NOT EXISTS (
  SELECT *
  FROM `tap`.`proc_privileges`
  WHERE `grantee` = gtee
  AND `routine_schema` != sname
  );
  
  RETURN rtn; 
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_single_table_priv` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81)) RETURNS TINYINT(1) BEGIN
  DECLARE rtn INT;

  SELECT COUNT(DISTINCT `table_name`) INTO rtn
  FROM `information_schema`.`table_privileges`
  WHERE `grantee` = gtee
  AND `table_schema` = sname
  AND `table_name` = tname
  AND NOT EXISTS (
    SELECT *
    FROM `information_schema`.`table_privileges`
    WHERE `grantee` = gtee
    AND `table_name` != tname
  )
  AND NOT EXISTS (
    SELECT *
    FROM `information_schema`.`user_privileges`
    WHERE `grantee` = gtee
    AND _table_privs(`privilege_type`) > 0 
  )
  AND NOT EXISTS (
    SELECT *
    FROM `information_schema`.`schema_privileges`
    WHERE `grantee` = gtee
    AND _table_privs(`privilege_type`) > 0
  )
  AND NOT EXISTS (
    SELECT *
    FROM `information_schema`.`column_privileges`
    WHERE `grantee` = gtee
    AND `table_name` != tname
  );
  
  RETURN rtn; 
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_subpartition_expression` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64)) RETURNS LONGTEXT CHARSET utf8mb4 BEGIN
  DECLARE ret LONGTEXT;

  SELECT TRIM(`subpartition_expression`) INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `subpartition_name` = subp;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_subpartition_method` (`sname` VARCHAR(64), `tname` VARCHAR(64), `subp` VARCHAR(64)) RETURNS VARCHAR(12) CHARSET utf8mb4 BEGIN
DECLARE ret VARCHAR(12);

  SELECT `subpartition_method` INTO ret
  FROM `information_schema`.`partitions`
  WHERE `table_schema` = sname
  AND `table_name` = tname
  AND `subpartition_name` = subp;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_table_character_set` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(32);

  SELECT c.`character_set_name` INTO ret
  FROM `information_schema`.`tables` AS t
  INNER JOIN `information_schema`.`collation_character_set_applicability` AS c
    ON (t.`table_collation` = c.`collation_name`)
  WHERE t.`table_schema` = sname
  AND t.`table_name` = tname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_table_collation` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(32);

  SELECT `table_collation` INTO ret
  FROM `information_schema`.`tables`
  WHERE `table_schema` = sname
  AND `table_name` = tname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_table_engine` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS VARCHAR(32) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(32);

  SELECT `engine` INTO ret
  FROM `information_schema`.`tables`
  WHERE `table_schema` = sname
  AND `table_name` = tname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_table_privileges` (`sname` VARCHAR(64), `tname` VARCHAR(64), `gtee` VARCHAR(81)) RETURNS TEXT CHARSET utf8mb4 BEGIN
  DECLARE rtn TEXT;
   
  IF @rollup = 1 THEN    
    SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
    FROM
     ( SELECT `privilege_type`
       FROM `information_schema`.`user_privileges`
       WHERE `grantee` = gtee AND _table_privs(`privilege_type`) > 0 
     UNION 
       SELECT `privilege_type`
       FROM `information_schema`.`schema_privileges`
       WHERE `grantee` = gtee AND `table_schema` = sname AND _table_privs (`privilege_type`) > 0
     UNION
       SELECT `privilege_type`
       FROM `information_schema`.`table_privileges`
       WHERE `grantee` = gtee AND `table_schema` = sname AND `table_name` = tname
     ) u;
  ELSE
    SELECT GROUP_CONCAT(`privilege_type`) INTO rtn
    FROM `information_schema`.`table_privileges`
    WHERE `grantee` = gtee AND `table_schema` = sname AND `table_name` = tname;
  END IF;
  
  RETURN rtn;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_table_privs` (`ptype` VARCHAR(64)) RETURNS TINYINT(1) BEGIN
  RETURN FIND_IN_SET(ptype,
    'ALTER,CREATE,CREATE VIEW,DELETE,DROP,GRANT,INDEX,INSERT,REFERENCES,SELECT,SHOW VIEW,TRIGGER,UPDATE'); 
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_table_sha1` (`sname` VARCHAR(64), `tname` VARCHAR(64)) RETURNS CHAR(40) CHARSET utf8mb4 BEGIN
  DECLARE ret CHAR(40);

  SELECT SHA1(GROUP_CONCAT(sha)) INTO ret
  FROM 
    (
      (SELECT SHA1( 
        GROUP_CONCAT(
          SHA1(
             CONCAT_WS('',`table_catalog`,`table_schema`,`table_name`,`column_name`,
              `ordinal_position`,`column_default`,`is_nullable`,`data_type`,
              `character_maximum_length`,`character_octet_length`,`numeric_precision`,
	            `numeric_scale`,`datetime_precision`,`character_set_name`,`collation_name`,
              `column_type`,`column_key`,`extra`,`privileges`,`column_comment`,
              `generation_expression`,`srs_id`)
          ))) sha
      FROM `information_schema`.`columns`
      WHERE `table_schema` = sname
      AND `table_name` = tname
      ORDER BY `table_name` ASC,`column_name` ASC) 
  UNION ALL
      (SELECT SHA1( 
        GROUP_CONCAT(
          SHA1(
            CONCAT_WS('',`constraint_catalog`,`constraint_schema`,`constraint_name`,
            `unique_constraint_catalog`,`unique_constraint_schema`,`unique_constraint_name`,
            `match_option`,`update_rule`,`delete_rule`,`table_name`,`referenced_table_name`)
      ))) sha
      FROM `information_schema`.`referential_constraints`
      WHERE `constraint_schema` = sname
      AND `table_name` = tname
      ORDER BY `table_name` ASC,`constraint_name` ASC)
  UNION ALL
      (SELECT SHA1( 
        GROUP_CONCAT(
          SHA1(
            CONCAT_WS('',`table_catalog`,`table_schema`,`table_name`,`non_unique`,
              `index_schema`,`index_name`,`seq_in_index`,`column_name`,`collation`,`cardinality`,
              `sub_part`,`packed`,`nullable`,`index_type`,`comment`,`index_comment`,`is_visible`)
      ))) sha
      FROM `information_schema`.`statistics`
      WHERE `table_schema` = sname
      AND `table_name` = tname
      ORDER BY `table_name` ASC,`index_name` ASC,`seq_in_index` ASC)
  UNION ALL
      (SELECT SHA1( 
        GROUP_CONCAT(
          SHA1(
           CONCAT_WS('',`trigger_catalog`,`trigger_schema`,`trigger_name`,`event_manipulation`,
            `event_object_catalog`,`event_object_schema`,`event_object_table`,`action_order`,
            `action_condition`,`action_statement`,`action_orientation`,`action_timing`,
            `action_reference_old_table`,`action_reference_new_table`,`action_reference_old_row`,
            `action_reference_new_row`,`sql_mode`,`definer`,`database_collation`)
      ))) sha
      FROM `information_schema`.`triggers`
      WHERE `trigger_schema` = sname
      AND `event_object_table` = tname
      ORDER BY `event_object_table` ASC,`trigger_name` ASC)
 ) objects;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_tap` (`aok` BOOLEAN, `test_num` INTEGER, `descr` TEXT, `todo_why` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    RETURN concat(CASE aok WHEN TRUE THEN '' ELSE 'not ' END,
        'ok ', test_num,
        CASE descr WHEN '' THEN '' ELSE COALESCE( concat(' - ', substr(diag( descr ), 3)), '' ) END,
        COALESCE( concat(' ', diag( concat('TODO ', todo_why) )), ''),
        CASE WHEN aok THEN '' ELSE concat('\n',
            diag(concat('Failed ',
                CASE WHEN todo_why IS NULL THEN '' ELSE '(TODO) ' END,
                'test ', test_num,
                CASE descr WHEN '' THEN '' ELSE COALESCE(concat(': "', descr, '"'), '') END,
                CASE WHEN aok IS NULL THEN concat('\n', '    (test result was NULL)') ELSE '' END
        ))) END
    );
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_timezones_updated` () RETURNS TINYINT(1) BEGIN

  DECLARE pre DATETIME;
  DECLARE post DATETIME;
  
  SET pre =  (SELECT CONVERT_TZ('2007-03-11 2:00:00','US/Eastern','US/Central'));
  SET post = (SELECT CONVERT_TZ('2007-03-11 3:00:00','US/Eastern','US/Central'));
 
  RETURN IF(pre = post, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_todo` () RETURNS TEXT CHARSET utf8mb4 MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
    
    
    
    DECLARE todos   INTEGER DEFAULT _get_latest_value('todo');
    DECLARE todo_id INTEGER;
    DECLARE note    TEXT;

    IF todos IS NULL THEN
        
        RETURN NULL;
    END IF;

    SET todo_id = _get_latest_id('todo');
    IF todos = 0 THEN
        
        DELETE FROM __tcache__ WHERE id = todo_id;
        RETURN NULL;
    END IF;
    
    IF todos <> -1 THEN
        CALL _idset(todo_id, todos - 1);
    END IF;

    SET note = _get_note_by_id(todo_id);
    IF todos = 1 THEN
        
        DELETE FROM __tcache__ WHERE id = todo_id;
    END IF;
    RETURN note;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_trigger_event` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64)) RETURNS VARCHAR(6) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(6);

  SELECT `event_manipulation` INTO ret
  FROM `information_schema`.`triggers`
  WHERE `event_object_schema` = sname
  AND `event_object_table` = tname
  AND `trigger_name` = trgr;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_trigger_is` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64)) RETURNS LONGTEXT CHARSET utf8mb4 BEGIN
  DECLARE ret LONGTEXT;

  SELECT `action_statement` INTO ret
  FROM `information_schema`.`triggers`
  WHERE `event_object_schema` = sname
  AND `event_object_table` = tname
  AND `trigger_name` = trgr;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_trigger_order` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64)) RETURNS BIGINT(20) BEGIN
  DECLARE ret BIGINT;

  SELECT `action_order` INTO ret
  FROM `information_schema`.`triggers`
  WHERE `event_object_schema` = sname
  AND `event_object_table` = tname
  AND `trigger_name` = trgr;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_trigger_timing` (`sname` VARCHAR(64), `tname` VARCHAR(64), `trgr` VARCHAR(64)) RETURNS VARCHAR(6) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(6);

  SELECT `action_timing` INTO ret
  FROM `information_schema`.`triggers`
  WHERE `event_object_schema` = sname
  AND `event_object_table` = tname
  AND `trigger_name` = trgr;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_unalike` (`res` BOOLEAN, `got` TEXT, `pat` TEXT, `descr` TEXT) RETURNS TEXT CHARSET utf8mb4 BEGIN
    IF res THEN RETURN  ok( res, descr ); END IF;
    RETURN concat(ok(res, descr), '\n',  diag(concat(
           '                  ', COALESCE( quote(got), 'NULL' ),
        '\n          matches: ', COALESCE( quote(pat), 'NULL' )
    )));
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_user_has_lifetime` (`hname` CHAR(60), `uname` CHAR(32)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM tap.mysql__user
  WHERE `Host` = hname
  AND `User` = uname
  AND `password_lifetime` IS NOT NULL AND `password_lifetime` != 0;

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_user_ok` (`hname` CHAR(60), `uname` CHAR(32)) RETURNS TINYINT(1) BEGIN
  DECLARE ret BOOLEAN;

  SELECT 1 INTO ret
  FROM tap.mysql__user
  WHERE `host` = hname
  AND `user` = uname
  AND `password_expired` <> 'Y'
  AND `account_locked` <> 'Y';

  RETURN COALESCE(ret, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_view_check_option` (`sname` VARCHAR(64), `vname` VARCHAR(64)) RETURNS VARCHAR(8) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(8);

  SELECT `check_option` INTO ret
  FROM `information_schema`.`views`
  WHERE `table_schema` = sname
  AND `table_name` = vname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_view_definer` (`sname` VARCHAR(64), `vname` VARCHAR(64)) RETURNS VARCHAR(93) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(93);

  SELECT `definer` INTO ret
  FROM `information_schema`.`views`
  WHERE `table_schema` = sname
  AND `table_name` = vname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_view_is_updatable` (`sname` VARCHAR(64), `vname` VARCHAR(64)) RETURNS VARCHAR(3) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(3);

  SELECT `is_updatable` INTO ret
  FROM `information_schema`.`views`
  WHERE `table_schema` = sname
  AND `table_name` = vname;

  RETURN COALESCE(ret, NULL);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `_view_security_type` (`sname` VARCHAR(64), `vname` VARCHAR(64)) RETURNS VARCHAR(7) CHARSET utf8mb4 BEGIN
  DECLARE ret VARCHAR(7);

  SELECT `security_type` INTO ret
  FROM `information_schema`.`views`
  WHERE `table_schema` = sname
  AND `table_name` = vname;

  RETURN COALESCE(ret, NULL);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `proc_privileges`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `proc_privileges` (
`GRANTEE` varchar(145)
,`ROUTINE_SCHEMA` char(64)
,`ROUTINE_NAME` char(64)
,`ROUTINE_TYPE` varchar(12)
,`PRIVILEGE_TYPE` varchar(13)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `__tcache__`
--

CREATE TABLE `__tcache__` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `label` text NOT NULL,
  `value` int(11) NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `__tresults__`
--

CREATE TABLE `__tresults__` (
  `numb` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `ok` tinyint(1) NOT NULL DEFAULT 1,
  `aok` tinyint(1) NOT NULL DEFAULT 1,
  `descr` text NOT NULL,
  `type` text NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur untuk view `proc_privileges`
--
DROP TABLE IF EXISTS `proc_privileges`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `proc_privileges`  AS  select concat('\'',`mysql`.`procs_priv`.`User`,'\'@\'',`mysql`.`procs_priv`.`Host`,'\'') AS `GRANTEE`,`mysql`.`procs_priv`.`Db` AS `ROUTINE_SCHEMA`,`mysql`.`procs_priv`.`Routine_name` AS `ROUTINE_NAME`,`mysql`.`procs_priv`.`Routine_type` AS `ROUTINE_TYPE`,'EXECUTE' AS `PRIVILEGE_TYPE` from `mysql`.`procs_priv` where find_in_set('EXECUTE',`mysql`.`procs_priv`.`Proc_priv`) > 0 union select concat('\'',`mysql`.`procs_priv`.`User`,'\'@\'',`mysql`.`procs_priv`.`Host`,'\'') AS `GRANTEE`,`mysql`.`procs_priv`.`Db` AS `ROUTINE_SCHEMA`,`mysql`.`procs_priv`.`Routine_name` AS `ROUTINE_NAME`,`mysql`.`procs_priv`.`Routine_type` AS `ROUTINE_TYPE`,'ALTER ROUTINE' AS `PRIVILEGE_TYPE` from `mysql`.`procs_priv` where find_in_set('ALTER ROUTINE',`mysql`.`procs_priv`.`Proc_priv`) > 0 union select concat('\'',`mysql`.`procs_priv`.`User`,'\'@\'',`mysql`.`procs_priv`.`Host`,'\'') AS `GRANTEE`,`mysql`.`procs_priv`.`Db` AS `ROUTINE_SCHEMA`,`mysql`.`procs_priv`.`Routine_name` AS `ROUTINE_NAME`,`mysql`.`procs_priv`.`Routine_type` AS `ROUTINE_TYPE`,'GRANT' AS `PRIVILEGE_TYPE` from `mysql`.`procs_priv` where find_in_set('GRANT',`mysql`.`procs_priv`.`Proc_priv`) > 0 ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `__tcache__`
--
ALTER TABLE `__tcache__`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `__tcache__`
--
ALTER TABLE `__tcache__`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=909;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
