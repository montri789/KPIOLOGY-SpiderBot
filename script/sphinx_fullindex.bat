c:
cd \sphinx\bin

net stop SphinxSearch
indexer.exe --all --config c:\sphinx\sphinx.conf
net start SphinxSearch