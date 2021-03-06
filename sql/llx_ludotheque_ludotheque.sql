-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.


CREATE TABLE llx_ludotheque_ludotheque(
	rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
	-- BEGIN MODULEBUILDER FIELDS
	libelle VARCHAR(255),
	fk_gerant INTEGER,
	fk_user_creat INTEGER,
	date_creat DATETIME,
	fk_user_modif INTEGER,
	tms TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
