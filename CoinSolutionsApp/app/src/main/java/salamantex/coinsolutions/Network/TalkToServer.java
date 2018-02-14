package salamantex.coinsolutions.Network;

import android.support.v4.content.LocalBroadcastManager;
import android.util.Log;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import android.content.Intent;
import android.app.IntentService;

import java.io.DataOutputStream;
import java.nio.charset.StandardCharsets;



public class TalkToServer extends IntentService {

    private int serverResponseCode;
    private String myurl;
    private String data;
    private LocalBroadcastManager broadcaster;

    public TalkToServer() {
        super("TalkToServer");
    }

    @Override
    protected void onHandleIntent(Intent intent) {
        serverResponseCode = 0;
        broadcaster = LocalBroadcastManager.getInstance(this);
        //Get the URL
        myurl = intent.getStringExtra("url").replaceAll(" ","%20");
        //Get the data
        data = intent.getStringExtra("data");

        Log.e("TAG", "Attempting to make call to the server...");
        Log.e("TAG", "URL is: "+myurl);
        serve();
    }

    private void serve() {
        try {
            URL url = new URL(myurl);
            byte[] postData       = data.getBytes( StandardCharsets.UTF_8 );
            int    postDataLength = postData.length;

            // Make the request object
            HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();

            // Set the post data in the request
            urlConnection.setDoOutput( true );
            urlConnection.setInstanceFollowRedirects( false );
            urlConnection.setRequestMethod( "POST" );
            urlConnection.setRequestProperty( "Content-Type", "application/x-www-form-urlencoded");
            urlConnection.setRequestProperty( "charset", "utf-8");
            urlConnection.setRequestProperty( "Content-Length", Integer.toString( postDataLength ));
            urlConnection.setUseCaches( false );

            // Copy the post data in
            try( DataOutputStream wr = new DataOutputStream( urlConnection.getOutputStream())) {
                wr.write( postData );
            }

            // Get the return string
            BufferedReader in = new BufferedReader(new InputStreamReader(urlConnection.getInputStream()));
            StringBuilder theReply = new StringBuilder();

            String inputLine;
            while ((inputLine = in.readLine()) != null) {
                theReply.append(inputLine);
            }

            in.close();
            Log.e("TAG", "Reply:");
            Log.e("TAG", theReply.toString());
            Intent intent = new Intent();
            intent.addFlags(Intent.FLAG_INCLUDE_STOPPED_PACKAGES);
            if (myurl.contains("createUser.php")) {
                intent.setAction("serverResponseRegister");
            }
            else if (myurl.contains("login.php")) {
                intent.setAction("serverResponseLogin");
            }
            else if (myurl.contains("addCurrency.php")) {
                intent.setAction("serverResponseCurrency");
            }
            else if (myurl.contains("createTransaction.php")) {
                intent.setAction("serverResponseTransaction");
            }
            intent.putExtra("result",theReply.toString());
            broadcaster.sendBroadcast(intent);
        }
        catch (Exception e) {
            Log.e("TAG", "Error making connection");
        }
    }
}

