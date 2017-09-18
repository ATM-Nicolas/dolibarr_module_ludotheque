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


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_ludotheque_ludotheque ADD INDEX idx_produit_user_creat (fk_user_creat);
ALTER TABLE llx_ludotheque_ludotheque ADD INDEX idx_produit_user_modif (fk_user_modif);
ALTER TABLE llx_ludotheque_ludotheque ADD INDEX idx_produit_gerant (fk_gerant);
-- END MODULEBUILDER INDEXES

ALTER TABLE llx_ludotheque_ludotheque ADD CONSTRAINT fk_ludotheque_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_ludotheque_ludotheque ADD CONSTRAINT fk_ludotheque_user_modif FOREIGN KEY (fk_user_modif) REFERENCES llx_user(rowid);
ALTER TABLE llx_ludotheque_ludotheque ADD CONSTRAINT fk_ludotheque_gerant FOREIGN KEY (fk_gerant) REFERENCES llx_societe(rowid);

