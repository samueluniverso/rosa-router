CREATE ROLE rosa WITH LOGIN PASSWORD 'rosa';
CREATE DATABASE rosa_api;
ALTER DATABASE rosa_api OWNER TO rosa;
GRANT ALL ON DATABASE rosa_api TO rosa;

CREATE TABLE sys_api_keys(
	id SERIAL NOT NULL PRIMARY KEY,
	audience TEXT NOT NULL,
	key TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL,
	revoked_at TIMESTAMP
);
CREATE INDEX idx_sys_api_keys_audience ON sys_api_keys(audience);
CREATE INDEX idx_sys_api_keys_key ON sys_api_keys(key);

CREATE TABLE sys_api_tokens(
	id SERIAL NOT NULL PRIMARY KEY,
	audience TEXT NOT NULL,
	token TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL,
	revoked_at TIMESTAMP
);
CREATE INDEX sys_api_tokens_audience ON sys_api_tokens(audience);
CREATE INDEX sys_api_tokens_key ON sys_api_tokens(token);
