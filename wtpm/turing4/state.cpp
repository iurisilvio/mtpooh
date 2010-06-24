#include "machine.h"

state::state()
{
  action.clear();
}

state::state(string n)
{
  name=n;
  action = *(new map<char, result>());
  final=false;
}
