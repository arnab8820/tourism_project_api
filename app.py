from flask import Flask, request
from flask_restful import Resource, Api
import mysql.connector

app = Flask(__name__)
api = Api(app)

# database connection
conn = mysql.connector.connect(host='13.127.126.240', db='tourism', user='dbuser', password='password')
if conn.is_connected():
    print("Connected to database")
cursor = conn.cursor(dictionary=True)


# API definitions
class GetNearby(Resource):
    def get(self):
        lat = float(request.args.get('lat'))
        lon = float(request.args.get('lon'))
        sql = "select place_id, place_name, round(ST_Distance_Sphere(place_position, ST_GeomFromText('POINT(%s %s)', " \
              "4326))/1000) as distance from place order by distance"
        cursor.execute(sql, (lat, lon))
        data = cursor.fetchall()
        return data


class GetPlaceDetail(Resource):
    def get(self):
        pid = int(request.args.get('place_id'))
        lat = float(request.args.get('lat'))
        lon = float(request.args.get('lon'))
        sql = "select place_name, place_description, ST_Latitude(place_position) as lat, ST_Longitude(place_position) " \
              "as lon, round(ST_Distance_Sphere(place_position, ST_GeomFromText('POINT(%s %s)', 4326))/1000) as " \
              "distance from place where place_id=%s "
        cursor.execute(sql, (lat, lon, pid))
        data = cursor.fetchone()
        return data


api.add_resource(GetNearby, "/getnearby")
api.add_resource(GetPlaceDetail, "/getplacedetail")

if __name__ == '__main__':
    app.run(debug=True)
