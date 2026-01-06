-- Migrate recommendations to reference residents instead of users
-- Adds resident_id columns and drops legacy patient_id fields
ALTER TABLE recommendations
    ADD COLUMN resident_id INT NULL AFTER id;

UPDATE recommendations r
LEFT JOIN patient_children pc ON pc.user_id = r.patient_id
SET r.resident_id = pc.resident_id
WHERE r.resident_id IS NULL;

ALTER TABLE recommendations
    ADD CONSTRAINT fk_rec_resident FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE;

ALTER TABLE recommendations
    DROP FOREIGN KEY fk_rec_patient,
    DROP COLUMN patient_id;

ALTER TABLE fulfillment_orders
    ADD COLUMN resident_id INT NULL AFTER recommendation_id;

UPDATE fulfillment_orders fo
JOIN recommendations r ON r.id = fo.recommendation_id
SET fo.resident_id = r.resident_id
WHERE fo.resident_id IS NULL;

ALTER TABLE fulfillment_orders
    ADD CONSTRAINT fk_fulfillment_resident FOREIGN KEY (resident_id) REFERENCES residents(id) ON DELETE CASCADE;

ALTER TABLE fulfillment_orders
    DROP FOREIGN KEY fk_fulfillment_patient,
    DROP COLUMN patient_id;
