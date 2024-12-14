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
CREATE INDEX CONCURRENTLY idx_sys_api_keys_audience ON sys_api_keys(audience);
CREATE INDEX CONCURRENTLY idx_sys_api_keys_key ON sys_api_keys(key);

CREATE TABLE sys_api_tokens(
	id SERIAL NOT NULL PRIMARY KEY,
	audience TEXT NOT NULL,
	type TEXT NOT NULL,
	token TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL,
	revoked_at TIMESTAMP
);
CREATE INDEX CONCURRENTLY sys_api_tokens_audience ON sys_api_tokens(audience);
CREATE INDEX CONCURRENTLY sys_api_tokens_key ON sys_api_tokens(token);

CREATE TABLE sys_api_logs(
    id SERIAL NOT NULL PRIMARY KEY,
    subject TEXT NOT NULL,
    client_token TEXT,
    client_key TEXT,
    remote_address TEXT NOT NULL,
    target_address TEXT NOT NULL,
    user_agent TEXT,
    request_method TEXT NOT NULL,
    request_uri TEXT NOT NULL,
    request_body TEXT,
    endpoint TEXT NOT NULL,
    class TEXT NOT NULL,
    method TEXT NOT NULL,
    access_at TIMESTAMP NOT NULL
);
CREATE INDEX CONCURRENTLY sys_api_logs_subject ON sys_api_logs(subject);
CREATE INDEX CONCURRENTLY sys_api_logs_remote_address ON sys_api_logs(remote_address);
CREATE INDEX CONCURRENTLY sys_api_logs_target_address ON sys_api_logs(target_address);
CREATE INDEX CONCURRENTLY sys_api_logs_request_method ON sys_api_logs(request_method);
CREATE INDEX CONCURRENTLY sys_api_logs_endpoint ON sys_api_logs(endpoint);
CREATE INDEX CONCURRENTLY sys_api_logs_class ON sys_api_logs(class);
CREATE INDEX CONCURRENTLY sys_api_logs_method ON sys_api_logs(method);
CREATE INDEX CONCURRENTLY sys_api_logs_access_at ON sys_api_logs(access_at);
