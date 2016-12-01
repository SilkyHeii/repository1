#include <stdio.h>
#include <stdlib.h>
#include <opencv/cv.h>
#include <opencv/cv.hpp>
#include <opencv/cxcore.h>
#include <opencv/highgui.h>
#include <opencv2/opencv.hpp>
#include <math.h>
#include <iostream>
#include <opencv2/core/core.hpp>
#include <opencv2/core/core_c.h>
#include <unistd.h>
#include <algorithm>
#include <vector>
#include <functional>
#include <sys/types.h>
#include <sys/stat.h>
#include <dirent.h>

using namespace cv;
using namespace std;

void conv(const Mat& src,const Mat& wind,vector<double>& v,double& _max){
    //このサブルーチンは窓画像を元画像と畳み込みその結果をベクトルに格納、最大値をmaxに格納している
    const Mat& src_img=src;
    const Mat& bb_img=wind;
    Mat dst(bb_img.rows,bb_img.cols,CV_64F);
    Scalar con;
    std::vector<double>& m_v=v;

    for(int x=0;x<(src_img.cols-bb_img.cols);++x){
        con=0;
         for(int y=0;y<(src_img.rows-bb_img.rows);++y){
            Mat roi = src_img(Rect(x,y,bb_img.cols,bb_img.rows));
            multiply(bb_img,roi,dst,1);
            con+=sum(dst);
        }
        m_v.push_back((double)con[0]);
    }
    double& max=_max;
    max=v[0];
    for(int j=0;j<(int)m_v.size();j++){
        if(m_v[j]>max){
            max=m_v[j];
        }
    }
}

//フォルダの中身を参照する
vector<string> InputPicturePath(string _path){
    vector<string> Path;
    string path = _path;
    DIR* dp=opendir(path.c_str());
    string CommonPath=path;
    string OriginPath;
    string PathName;
    int i=0;
    if (dp!=NULL)
    {
        struct dirent* dent;
        do{
            dent = readdir(dp);
            if (dent!=NULL){
                if(i>1){
                    OriginPath=dent->d_name;
                    PathName=CommonPath+OriginPath;
                    Path.push_back(PathName);
//                    cout<<PathName<<endl;
                }
                i++;
            }
        }while(dent!=NULL);
        closedir(dp);
    }
    return Path;
}

void verticalCut(int _horizonnum){
    int num = _horizonnum;
    cout<<num<<endl;
    for(int i=0;i<num;i++){
        cout<<"i="<<i<<endl;
        string path="./horizontal/horizontal"+to_string(i);
        vector<string> horizontalPath=InputPicturePath(path);
        struct stat st;
        string dirname = "./output/output"+to_string(i);
        if(stat(dirname.c_str(), &st) != 0){
             mkdir(dirname.c_str(), 0775);
        }
        //変数定義および初期化
        for(int j=0;j<horizontalPath.size();j++){
               cout<<"j="<<j<<endl;
               struct stat st;
               string dirname = "./output/output"+to_string(i)+"/output"+to_string(j);
               if(stat(dirname.c_str(), &st) != 0){
                    mkdir(dirname.c_str(), 0775);
               }
               Mat src_gray_img=imread("./horizontal/horizontal"+to_string(i)+"/dst"+to_string(j)+".jpg",0);
               //Mat src_raw_img=imread("./horizontal/horizontal"+to_string(i)+"/dst"+to_string(j)+".jpg",1);
               //Mat line_img=imread("./horizontal/horizontal"+to_string(i)+"/dst"+to_string(j)+".jpg",0);
               vector<const Mat*> window;
               Mat bb_img=imread("./bb.png",0);
               Mat bw_img=imread("./bw.png",0);
               Mat wb_img=imread("./wb.png",0);
               Mat ww_img=imread("./ww.png",0);
               Mat diff(1,src_gray_img.cols , CV_64F);
               Mat m2 = (cv::Mat_<double>)src_gray_img;
               Mat m3,m4,afinleft,afinright;
               Mat v1;
               Mat maxDiff;

               Mat srcleft = m2;
               Mat srcright = m2;

               const Point2f leftsrc_pt[] = { Point2f(0,0), Point2f(srcleft.cols-1, 0), Point2f(0,srcleft.rows-1) };
               const Point2f leftdst_pt[] = { Point2f(srcleft.rows*0.02, 0), Point2f(srcleft.cols-1,0), Point2f(0,srcleft.rows-1) };            //アフィン変換を用いて傾きを補正する（剪断操作）
               Mat leftwarp_mat = getAffineTransform(leftsrc_pt,leftdst_pt);
               warpAffine(srcleft,afinleft,leftwarp_mat,srcleft.size());

               const Point2f src_pt[] = { Point2f(0,0), Point2f(srcright.cols-1, 0), Point2f(0,srcright.rows-1) };
               const Point2f dst_pt[] = { Point2f(-srcright.rows*0.0,0), Point2f(srcright.cols-1,0), Point2f(0,srcright.rows-1) };            //アフィン変換を用いて傾きを補正する（剪断操作）
               Mat warp_mat = getAffineTransform(src_pt,dst_pt);
               warpAffine(srcright,afinright,warp_mat,srcright.size());

               afinright(Rect(m2.cols/2.0,0,m2.cols/2.0,m2.rows)).copyTo(afinleft(Rect(m2.cols/2.0,0,m2.cols/2.0,m2.rows)));
               Mat afin_img = afinleft.clone();
               Mat G_afin_img;

               //afin_imgにガウシンアンフィルタをかける
               GaussianBlur(afin_img,G_afin_img,Size(7,7),10,10);

               m2=afinleft;

               Mat canny_img;
               Canny(src_gray_img,canny_img,50,200);

               /*
               Mat avecols,ave;
               int offset;
               //画像の平均をとって明度を変更しようとした↓
               reduce(m2, avecols, 0, CV_REDUCE_AVG);
               reduce(avecols, ave, 1, CV_REDUCE_AVG);
               offset=127-ave.at<double>(0,0);
               //cout<<"offset="<<offset<<endl;
               m2=m2+offset;
               */

               //シグモイド関数を用いて画像のコントラストを上げてみる
               double a=30,b=1;
//               for(int y=0;y<G_afin_img.rows;++y){
//                     for(int x=0;x<G_afin_img.cols;++x){
//                        G_afin_img.at<double>(y,x)=255.0/(1+exp(-b*((double)G_afin_img.at<int>(y,x)-(255.0/2.0))));
//                     }
//               }
               Mat adaptive_img;
               adaptiveThreshold(src_gray_img, adaptive_img, 255, ADAPTIVE_THRESH_GAUSSIAN_C, THRESH_BINARY, 7, 8);
               //シグモイド関数を用いて画像のコントラストを上げてみる
               for(int y=0;y<m2.rows;++y){
                     for(int x=0;x<m2.cols;++x){
                        m2.at<double>(y,x)=255.0/(1+exp(-a*(m2.at<double>(y,x)-(255.0/2.0))));
                     }
               }

               m3=255-m2;   //輝度値反転

               reduce(m2, v1, 0, CV_REDUCE_SUM);    //各列の和をとる
               for(int y=0;y<v1.rows;++y){
                     for(int x=0;x<v1.cols;++x){
                         diff.at<double>(y,x)=fabs(v1.at<double>(y,x)-v1.at<double>(y,x-1));
                         //cout<<diff.at<double>(y,x)<<endl;
                     }
               }

               Mat castcanny= (cv::Mat_<double>)canny_img;
               Mat castadapimg=(cv::Mat_<double>)adaptive_img;
//               castcanny=10-castcanny;
               try{
                    castadapimg=castadapimg+castcanny-127;
               }catch(Exception e){
               }


               //cout<<diff<<endl;
               reduce(diff,maxDiff,1,CV_REDUCE_MAX);     //行の最大値を見つける
               //cout<<"maxDiff="<<maxDiff.at<double>(0,0)<<endl;
               diff=diff/maxDiff.at<double>(0,0);
               window.push_back(&bb_img);
               window.push_back(&bw_img);
               window.push_back(&wb_img);
               window.push_back(&ww_img);
               std::vector<vector<double>> m_v;
               m_v.resize(4);
               vector<double> max;
               max.resize(4);
               //vector<int> cutPoint;

               //namedWindow("test",CV_WINDOW_AUTOSIZE|CV_WINDOW_FREERATIO);
//               if(j==1){
//                   for(int y=0;y<v1.rows;++y){
//                         for(int x=0;x<v1.cols;++x){
//                            cout<<diff.at<double>(y,x)<<endl;
//                         }
//                   }
//                   cout<<"maxDiff="<<maxDiff.at<double>(0,0)<<endl;
//               }
               //微分式バージョン
               int count=0,startX=0,drop_image_width=75;
               for(int y=0;y<v1.rows;++y){
                     for(int x=0;x<v1.cols;++x){
                         if(diff.at<double>(y,x)>0.08){
                             //画像を分割する
                             cv::Mat roi =G_afin_img(cv::Rect(startX,0,x-startX,src_gray_img.rows));
                             if(roi.cols>drop_image_width){
                               cv::imwrite("./output/output"+to_string(i)+"/output"+to_string(j)+"/dst"+to_string(count)+".jpg",roi);
                               line(src_gray_img,Point(x,0),Point(x,src_gray_img.rows),Scalar(0,0,255),20,4);
                               count++;
                               startX=x;
                             }
                         }
                     }
               }
               cv::Mat roi =G_afin_img(cv::Rect(startX,0,src_gray_img.cols-startX,src_gray_img.rows));
               cv::imwrite("./output/output"+to_string(i)+"/output"+to_string(j)+"/dst"+to_string(count)+".jpg",roi);
               imwrite("./output/output"+to_string(i)+"/lineimg"+to_string(j)+".jpg",src_gray_img);

//               imwrite("/home/hirai/test/build-test-Desktop_Qt_5_7_0_GCC_64bit-Release/output/afintest"+to_string(j)+".jpg",afinleft);
               /*
                #pragma omp parallel for
                for(int k=0;k<4;k++){
                    conv(src_gray_img,*window[k],m_v[k],max[k]);
                }
                for(int m=0;m<m_v.size();m++){
                    const Mat& _window=*window[m];
                    vector<double>& v=m_v[m];
                    for(int n=0;n<v.size();n++){
                        if(v[n]/max[m]>0.999995){
                          //line(line_img, cv::Point(n-(_window.cols/2),0), cv::Point(n-(_window.cols/2),src_raw_img.rows), cv::Scalar(0,0,200),2, 4);
                          cutPoint.push_back((int)(n-(_window.cols/2)));
                        }
                    }
                }
                int count=0;
                int start=0;
                sort(cutPoint.begin(),cutPoint.end());
                for(int l=0;l<cutPoint.size();l++){
                    if((cutPoint[l]-start)>50 && (cutPoint[l+1]-cutPoint[l])>10){
                        //ある閾値をこえたカットポイントで画像を垂直方向に分割、この際光学文字認識を行いやすくするために分割した画像をcannyを用いて直線検出を行う予定どぅあ＾〜〜
                          Mat roi=src_raw_img(Rect(start,0,cutPoint[l]-start,src_raw_img.rows));
                          imwrite("./output/output"+to_string(i)+"/output"+to_string(j)+"/dst"+to_string(count)+".jpg",roi);
                          start=cutPoint[l];
                          count++;
                    }
                }
                Mat roi=src_raw_img(Rect(start,0,src_raw_img.cols-start,src_raw_img.rows));
                imwrite("./output/output"+to_string(i)+"/output"+to_string(j)+"/dst"+to_string(count)+".jpg",roi);
                //imwrite("conv_test.png",line_img);
                //imshow("test",line_img);
                //cvWaitKey(0);
                */
            }
        }
    }

int horizontalCut(vector<string> _picturePath){
    vector<string> picturePath = _picturePath;
    const int drop_image_height=200;

    for(int i=0;i<(int)picturePath.size();i++){
        struct stat st;
        string dirname = "./horizontal/horizontal"+to_string(i);
        if(stat(dirname.c_str(), &st) != 0){
             mkdir(dirname.c_str(), 0775);
        }
        Mat m1=imread(picturePath[i],0);
        Mat lineimg = m1.clone();
        cv::Mat m2 = (cv::Mat_<double>)m1;
        cv::Mat v1;
        Mat max;
        cv::Mat diff(m1.rows, 1, CV_64F);
        Mat ad;
        m1=255-m1;    //輝度値を反転する
        m2=255-m2;    //輝度値を反転する
        int count=0;
        int startY=0;

        cv::reduce(m2, v1, 1, CV_REDUCE_SUM); //行の成分の和を取る
        reduce(v1,max,0,CV_REDUCE_MAX);
        //cout<<max.at<double>(0,0)<<endl;
        for(int y=0;y<v1.rows;++y){
              for(int x=0;x<v1.cols;++x){
                  diff.at<double>(y,x)=v1.at<double>(y,x)-v1.at<double>(y-1,x);
              }
        }
        cv::abs(diff);
        reduce(diff,ad,0,CV_REDUCE_MAX);
        diff=diff/ad.at<double>(0,0);

        for(int y=0;y<v1.rows;++y){
              for(int x=0;x<v1.cols;++x){
                  //diff.at<double>(y,x)=v1.at<double>(y,x)-v1.at<double>(y-1,x);
                  //cout<<"diff.at(y,x)="<<diff.at<double>(y,x)<<endl;
                  //cout<<(v1.at<double>(y,x))/max.at<double>(0,0)<<endl;
                  /*条件にいれていたが除外した ＝＞(v1.at<double>(y,x))/max.at<double>(0,0)>0.999995||*/
                  if(diff.at<double>(y,x)>0.05){
                      //画像を分割する
                      cv::Mat roi = m1(cv::Rect(0,startY,m1.cols,y-startY));
                      if(roi.rows>drop_image_height){
                        cv::imwrite("./horizontal/horizontal"+to_string(i)+"/dst"+std::to_string(count)+".jpg",roi);
                        line(lineimg,Point(0,y),Point(lineimg.cols,y),Scalar(0,0,200),75,4);
                        count++;
                      }
                      startY=y;
                  }
              }
        }
        //画像を分割する
         cv::Mat roi = m1(cv::Rect(0,startY,m1.cols,m1.rows-startY));
         cv::imwrite("./horizontal/horizontal"+to_string(i)+"/dst"+std::to_string(count)+".jpg",roi);
         imwrite("./horizontal/lineimg"+to_string(i)+".jpg",lineimg);
    }
    return (int)picturePath.size();
}

int main(){
    int horizondirnum;
    vector<string> InputPicturePathName=InputPicturePath("/home/hirai/画像/input_picture/");
    horizondirnum=horizontalCut(InputPicturePathName);
    verticalCut(horizondirnum);
}
