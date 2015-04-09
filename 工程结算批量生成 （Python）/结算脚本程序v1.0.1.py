# -*- coding: utf-8 -*-

import win32com.client
import os.path
import argparse


#############################################################
#配置
#difine constrains
JIESUAN_EXCEL_FILE_NAME="全额结算台帐.xlsx"
JIESUAN_SHEET_NAME='台帐'
JIESUAN_TEMPLATE_FILE_NAME="全额结算模板.docm"

#############################################################

JIESUAN_DIRNAME=os.path.dirname(__file__)   
JIESUAN_FILE_PATH=os.path.join(JIESUAN_DIRNAME,JIESUAN_EXCEL_FILE_NAME)
JIESUAN_DOCM_FILE_PATH=os.path.join(JIESUAN_DIRNAME,JIESUAN_TEMPLATE_FILE_NAME)

#############################################################

parser=argparse.ArgumentParser(description='''结算程序''')
parser.add_argument("-s",
                    help='''Row_NO_START''',
                    type=int,
                    required=True)
parser.add_argument("-e",
                    help='''ROW_NO_END''',
                    type=int)

args=parser.parse_args()

ROW_NO_START=args.s
ROW_NO_END=args.e


class JieSuan_Info():
#'''结算信息，从excel中获取'''

    def __init__(self):
        self.xlsApp=win32com.client.Dispatch("Excel.Application")
        self.js_date=""
        self.js_ID=""
        self.js_name=""
        self.js_text=""
        self.js_doc_file_name=""

    def open_xls_sht(self,xls_file_path,xls_sht_name):
        try:
            self.xlsWkbk=self.xlsApp.Workbooks.Open(xls_file_path,ReadOnly=True)
            self.xlsSht=self.xlsWkbk.Sheets(xls_sht_name)
        except:
            print("cannot open: ",xls_file_path,xls_sht_name)
            exit(0)

    def get_js_field(self,row_No):
        if self.xlsSht:
            self.js_date=self.xlsSht.cells(row_No,2)
            self.js_ID=self.xlsSht.cells(row_No,3)
            self.js_name=self.xlsSht.cells(row_No,4)
            self.js_doc_file_name=self.xlsSht.cells(row_No,10)
            self.js_text=self.xlsSht.cells(row_No,13)
        else:
            print("no xlsSht!")
            exit(0)

    def exit_xls_App(self):
        if self.xlsWkbk:
            self.xlsWkbk.Close(SaveChanges=0)
            del self.xlsApp


class JieSuanWdDoc():
#'''结算证明'''

    def __init__(self):
        self.wdApp=win32com.client.Dispatch("Word.Application")
        self.save_as_file_name="undefined"
        self.js_date=""
        self.js_ID=""
        self.js_name=""
        self.js_text=""

    def open_wd_doc(self,wd_file_path):
        try:
            self.wdDoc=self.wdApp.Documents.Open(wd_file_path,ReadOnly=True)
            print("openning file:",self.wdDoc)

        except:
            print("cannot open ",wd_file_path)

    def set_bookmark(self,bookmark_name,val):
        try:
            if self.wdDoc.Bookmarks.Exists(bookmark_name):
                self.wdDoc.Bookmarks(bookmark_name).Range=val
            else:
                print("BookMarks:",bookmark_name,"does not exists")
        except:
            print("wrong in bookmark ")

    def set_save_as_file_name(self,val):
        self.save_as_file_name=str(val)

    def save_as(self,path):
        self.wdDoc.SaveAs(path)
        print("file saved:",path)

    def exit_wd_App(self):
        self.wdApp.Quit(False)


def gen_jiesuan_doc(row_no):

    try:
        js_info=JieSuan_Info()
        js_info.open_xls_sht(JIESUAN_FILE_PATH,JIESUAN_SHEET_NAME)
        js_info.get_js_field(row_no)

        js_wd=JieSuanWdDoc()
        js_wd.open_wd_doc(JIESUAN_DOCM_FILE_PATH)
        js_wd.set_save_as_file_name(js_info.js_doc_file_name)
        try:
            js_wd.set_bookmark("工程名称",js_info.js_name)
            js_wd.set_bookmark("工程编号",js_info.js_ID)
            js_wd.set_bookmark("工程结算正文",js_info.js_text)
            js_wd.set_bookmark("结算日期",js_info.js_date)
            js_wd.save_as(js_wd.save_as_file_name)#默认保存在“我的文档下”
        finally:
            js_wd.exit_wd_App()
    finally:
        js_info.exit_xls_App()


if __name__=="__main__":
    i_row_range=range(ROW_NO_START,ROW_NO_END+1)
    for i_row in i_row_range:
        gen_jiesuan_doc(i_row)



