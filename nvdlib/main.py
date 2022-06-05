import json
import os
import sys

import nvdlib

sys.stderr = sys.stdout

result = nvdlib.searchCVE(cpeName=sys.argv[1], keyword='',
                          exactMatch=True, key=sys.argv[2], limit=2000)

cpe = sys.argv[1].replace(":", "")
cpe = cpe.replace("*", "")

with open(os.path.dirname(os.path.realpath(__file__)) + "\\" + cpe, 'w') as f:
    f.write(json.dumps(result))
    print(f.name)
