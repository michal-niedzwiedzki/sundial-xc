INSERT INTO member (member_id, password, member_role, security_q, security_a, status, member_note, admin_note, join_date, expire_date, away_date, account_type, email_updates, balance)
VALUES ("ADMIN", sha1("password"), 9, NULL, NULL, "A", NULL, "Admin account", current_timestamp(), NULL, NULL, "S", 7, 0.00);

INSERT INTO person (person_id, member_id, primary_member, directory_list, first_name, last_name, mid_name, dob, mother_mn, email, address_city, address_state_code, address_post_code, address_country)
VALUES (1, "admin", "Y", "Y", "Special Admin", "Account", "", NULL, NULL, NULL, "", "", "", "");
