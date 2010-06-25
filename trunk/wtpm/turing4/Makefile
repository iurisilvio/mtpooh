CXX=g++
CXX_CFLAGS=-g -pedantic -ansi -Wall -Wno-long-long

SRCS_SHARED=state.cpp machine.cpp result.cpp tape.cpp
OBJS_SHARED=${SRCS_SHARED:.cpp=.o}
EXE=turing graph-gen

.PHONY: all clean

.SUFFIXES:
.SUFFIXES: .o .cpp

.cpp.o:
	$(CXX) $(CXX_CFLAGS) -c $<

all: $(EXE)

turing: $(OBJS_SHARED) turing.o
	$(CXX) $(OBJS_SHARED) turing.o -o $@

graph-gen: $(OBJS_SHARED) graph-gen.o
	$(CXX) $(OBJS_SHARED) graph-gen.o -o $@

clean:
	-rm *.o
	-rm $(EXE)
