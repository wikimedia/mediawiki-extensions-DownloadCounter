-- Create the table : 'downloads_files'

CREATE TABLE /*_*/downloads_files (
  filename      VARCHAR(255) NOT NULL, -- I guess, file name is unique ;o)
  downloaded    INT(5)       NOT NULL DEFAULT 0, -- By default, downloaded 0 time (could be 1)
  last_download INT(5)       NOT NULL DEFAULT 0, -- By default, downloaded 0 time (could be 1)

PRIMARY KEY(filename)
) /*$wgDBTableOptions*/;