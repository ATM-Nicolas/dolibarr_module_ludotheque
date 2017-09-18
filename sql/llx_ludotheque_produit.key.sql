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
ALTER TABLE llx_ludotheque_produit ADD INDEX idx_produit_emplacement (fk_emplacement);
ALTER TABLE llx_ludotheque_produit ADD INDEX idx_produit_categorie (fk_categorie);
-- END MODULEBUILDER INDEXES

ALTER TABLE llx_ludotheque_produit ADD CONSTRAINT fk_produit_emplacement FOREIGN KEY (fk_emplacement) REFERENCES llx_ludotheque(rowid);
ALTER TABLE llx_ludotheque_produit ADD CONSTRAINT fk_produit_categorie FOREIGN KEY (fk_categorie) REFERENCES llx_c_categorie_produit(rowid);
ALTER TABLE llx_ludotheque_produit ADD UNIQUE(fk_categorie, libelle, fk_emplacement);

