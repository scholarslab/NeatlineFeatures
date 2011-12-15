
-- First, create a temporary table to store the IDs of the items that have to
-- be scrubbed.
CREATE TEMPORARY TABLE items_to_del (item_id INTEGER);

-- This identifies the records that need to be cleaned up.
INSERT INTO items_to_del (item_id)
    SELECT record_id
    FROM omeka_element_texts
    WHERE element_id=50 AND text LIKE 'Cucumber:%';

-- Let's talk about it.
SELECT "DELETING", record_id, text
    FROM omeka_element_texts
    WHERE element_id=50 AND text LIKE 'Cucumber:%';

-- omeka_element_texts
DELETE FROM omeka_element_texts
    WHERE record_id IN (SELECT item_id FROM items_to_del);

-- omeka_files
-- I'm not handling files.

-- omeka_items
DELETE FROM omeka_items
    WHERE id IN (SELECT item_id FROM items_to_del);

-- Clean up the cleanup.
DROP TABLE items_to_del;

