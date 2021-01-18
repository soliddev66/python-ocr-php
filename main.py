import re
import os
import sys
import cv2
import pytesseract
from pytesseract import Output
import json

os.putenv("TESSDATA_PREFIX", "./tessdata")
img = cv2.imread(sys.argv[1])

custom_config = r'--oem 3 --psm 6 outputbase digits'
w, h = 53, 25
step_y = 48
tx, ty, tx1, ty1 = 183, 58, 937, 58

list1 = []
list2 = []
for i in range(13):
    crop_img = img[ty + i * step_y:ty + i * step_y + h, tx:tx + w]
    list1.append(float(pytesseract.image_to_string(crop_img, config=custom_config)))
    crop_img = img[ty1 + i * step_y:ty1 + i * step_y + h, tx1:tx1 + w]
    list2.append(float(pytesseract.image_to_string(crop_img, config=custom_config)))

data = {}
data['SunD'] = list1[0]
data['EarthD'] = list1[1]
data['NNodeD'] = list1[2]
data['SNodeD'] = list1[3]
data['MoonD'] = list1[4]
data['MercuryD'] = list1[5]
data['VenusD'] = list1[6]
data['MarsD'] = list1[7]
data['JupiterD'] = list1[8]
data['SaturnD'] = list1[9]
data['UranusD'] = list1[10]
data['NeptuneD'] = list1[11]
data['PlutoD'] = list1[12]

data['SunP'] = list2[0]
data['EarthP'] = list2[1]
data['NNodeP'] = list2[2]
data['SNodeP'] = list2[3]
data['MoonP'] = list2[4]
data['MercuryP'] = list2[5]
data['VenusP'] = list2[6]
data['MarsP'] = list2[7]
data['JupiterP'] = list2[8]
data['SaturnP'] = list2[9]
data['UranusP'] = list2[10]
data['NeptuneP'] = list2[11]
data['PlutoP'] = list2[12]

with open('data.json', 'w') as outfile:
    json.dump(data, outfile)

# print(*list1, sep = ", ")  
# print(*list2, sep = ", ")  
# cv2.imshow('img', img)
# cv2.waitKey(0)