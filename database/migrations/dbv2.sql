-- Do not use ENUMs. Use ints and dictionaries instead.
-- insured event = coverage = peril ?????????????????????????????????

DROP DATABASE IF EXISTS inxurenz;
CREATE DATABASE inxurenz;
USE inxurenz;




-- life, motor, home, landlord, travel, health, boat, ??? 'CIT', 'Crime', 'D&O', 'Medical Malpractice', 'P&T', 'PI'
CREATE TABLE insurance_types (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title varchar(50)
);

-- coverage options typically provided by insurers
CREATE TABLE coverages (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title varchar(100),
  insurance_type_id int NOT NULL,
  -- parent_id int,
  FOREIGN KEY (insurance_type_id) REFERENCES insurance_types(id)
);

CREATE TABLE perils (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title varchar(100) NOT NULL
);

-- pivot table M:M relationship between perils & coverage options



-- a specific coverage included in a policy
CREATE TABLE policy_coverages (
  policy_id int NOT NULL,
  coverage_id int NOT NULL,
  limit_amount decimal(10), -- max coverage
  n_days_to_file_claim int NOT NULL, -- time window to file claim (in days), ?? n_days_claimable
  FOREIGN KEY (policy_id) REFERENCES policies(id),
  FOREIGN KEY (coverage_id) REFERENCES coverages(id)
);

CREATE TABLE claims (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  policy_id int NOT NULL, -- maybe should be coverage_id instead
  claimant_id int NOT NULL, -- might be redundant if claimant is always going to be the policyholder
  -- damaged_party_id int NOT NULL, -- will this always be equal to the claimant_id?
  status ENUM ('filed', 'under review', 'approved', 'declined', 'disputed', 'withdrawn', 'paid') DEFAULT 'filed',
  accident_happened_at timestamp, -- loss_date
  accident_reported_at timestamp, -- reporting_date
  peril_id int NOT NULL, -- is this the same as class_of_business?
  description varchar(1000),
  filed_via ENUM ('phone call', 'email', 'fax', 'app'),
  FOREIGN KEY (policy_id) REFERENCES policies(id),
  FOREIGN KEY (claimant_id) REFERENCES persons(id),
  -- FOREIGN KEY (damaged_party_id) REFERENCES persons(id),
  FOREIGN KEY (peril_id) REFERENCES perils(id)
);

-- insurance adjuster investigates claim and recommends how much the insurance company should pay for the loss
CREATE TABLE investigations (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  claim_id int NOT NULL,
  adjuster_id int NOT NULL,
  description varchar(1000) NOT NULL,
  payout_estimate decimal(10,2) NOT NULL,
  FOREIGN KEY (claim_id) REFERENCES claims(id),
  FOREIGN KEY (adjuster_id) REFERENCES users(id) -- adjuster will be a user of the SaaS
);

/*
At any point in time the adjuster can create a `claim_node` which
essentially is a log entry listing any new recoveries, fees, and paids.
*/

CREATE TABLE claim_nodes (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  claim_id int NOT NULL,
  -- age int NOT NULL,
  -- claims_reserve decimal(10,2),
  currency ENUM ('USD', 'GBP', 'EUR'),
  paid int NOT NULL,
  -- outstanding int NOT NULL, -- needs to be calculated
  incurred int NOT NULL,
  -- total_fees int NOT NULL, -- needs to be calculate (sum of all fees)
  -- total_recoveries int NOT NULL, -- can be calculated 
  -- overall_amount int NOT NULL, -- this should be calculated
  status ENUM ('open', 'closed'),
  -- exposure int NOT NULL,
  CONSTRAINT UC_Stage UNIQUE (claim_id, age),
  FOREIGN KEY (claim_id) REFERENCES claims(id)
);

CREATE TABLE recoveries (
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  claim_node_id int NOT NULL,
  amount decimal(10,2) NOT NULL,
  description varchar(200),
  created_by int NOT NULL,
  FOREIGN KEY (claim_node_id) REFERENCES claim_nodes(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

-- a claim_node can have many fees
CREATE TABLE fees (
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  claim_node_id int NOT NULL,
  amount decimal(10,2) NOT NULL,
  invoice_number varchar(20),
  description varchar(200),
  created_by int NOT NULL,
  FOREIGN KEY (claim_node_id) REFERENCES claim_nodes(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

-- payments made to claimant
CREATE TABLE paids (
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  claim_node_id int NOT NULL,
  amount decimal(10,2) NOT NULL,
  transaction_number varchar(30),
  description varchar(200) NOT NULL,
  created_by int NOT NULL,
  FOREIGN KEY (claim_node_id) REFERENCES claim_nodes(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

-- CREATE TABLE cancellation_rights();