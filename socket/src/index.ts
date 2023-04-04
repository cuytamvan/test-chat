import Express from 'express';
import http from 'http';
import cors from 'cors';
import { json } from 'body-parser';
import { Server } from 'socket.io';


const app = Express();

app.use(json());
app.use(
  cors({
    origin: '*',
  })
);

const httpServer = http.createServer(app);

const io = new Server(httpServer, {
  cors: {
    origin: '*',
  },
});

let userOnline: { id: number; socketId: string }[] = [];

io.on('connection', (socket) => {
  const id = socket.handshake.query.id as string;

  userOnline = [...userOnline, {
    id: parseInt(id),
    socketId: socket.id,
  }];

  socket.on('message', (chatId: number, message: string, toId: number) => {
    const data = userOnline.find(r => toId === r.id);
    if (data) socket.to(data.socketId).emit('message', chatId, message, toId, id);
  });

  socket.on('disconnect', () => {
    const index = userOnline.findIndex(r => r.socketId === socket.id);
    if (index >= 0) userOnline.splice(index, 1);
  });

});

httpServer.listen(5000, () => {
  console.log(`server started at http://localhost:${5000}`);
});
