FLAGS=-g -pedantic -ansi -Wall -Wno-long-long
OBJ=state.o machine.o result.o tape.o

build: turing

state.o: state.cpp machine.h
	g++ state.cpp -o state.o -c $(FLAGS)

machine.o: machine.cpp machine.h
	g++ machine.cpp -o machine.o -c $(FLAGS)

result.o: result.cpp machine.h
	g++ result.cpp -o result.o -c $(FLAGS)

tape.o: tape.cpp machine.h
	g++ tape.cpp -o tape.o -c $(FLAGS)

turing: turing.cpp $(OBJ)
	g++ turing.cpp $(OBJ) -o turing $(FLAGS)

clear:
	rm *.o
	rm turing

clean:
	make clear
	make turing

tpmgg: tpmgg.cpp $(OBJ)
	g++ -DGRAPH_GEN machine.cpp -o machine_altered.o -c $(FLAGS)
	g++ -DGRAPH_GEN tpmgg.cpp state.o result.o tape.o machine_altered.o -o tpmgg $(FLAGS)
